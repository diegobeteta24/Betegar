<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup'
        . ' {--keep=4 : Número máximo de respaldos a conservar}'
        . ' {--connection=mysql : Conexión de base de datos a usar}';

    protected $description = 'Genera un backup SQL y aplica retención (máx N archivos)';

    public function handle(): int
    {
        $connection = config("database.connections." . $this->option('connection'));
        if (!$connection) {
            $this->error('Conexión no encontrada.');
            return self::FAILURE;
        }

        if ($connection['driver'] !== 'mysql') {
            $this->error('Sólo implementado para MySQL/MariaDB por ahora.');
            return self::FAILURE;
        }

        $keep = (int)$this->option('keep');
        $disk = Storage::disk('local');
        $dir = 'backups';
        if (!$disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }
        // Asegurar en filesystem real también
        if (!is_dir(storage_path('app/'.$dir))) {
            mkdir(storage_path('app/'.$dir), 0775, true);
        }

        $timestamp = now()->format('Ymd_His');
        $filename = "backup_{$timestamp}.sql.gz";
        $path = storage_path("app/{$dir}/{$filename}");

        $host = $connection['host'] ?? '127.0.0.1';
        $port = $connection['port'] ?? 3306;
        $user = $connection['username'];
        $pass = $connection['password'];
        $db   = $connection['database'];

        $this->info("Creando backup: {$filename}");

        $dumpParts = [
            'mysqldump',
            '--no-tablespaces', // evita error de privilegios PROCESS
            '-h'.$host,
            '-P'.$port,
            '-u'.$user,
        ];
        if ($pass) {
            $dumpParts[] = '-p'.$pass; // CLI warning acceptable
        }
        $dumpParts[] = $db;
        // Construir comando completo para bash -c
        $cmd = implode(' ', array_map('escapeshellcmd', $dumpParts)) . ' | gzip > '.escapeshellarg($path);
        $command = ['bash','-c',$cmd];

        $process = new Process($command, base_path(), null, null, 600);
        $process->run();
        if (!$process->isSuccessful()) {
            $this->error('Error al ejecutar mysqldump: '.$process->getErrorOutput());
            if ($disk->exists("{$dir}/{$filename}")) {
                $disk->delete("{$dir}/{$filename}");
            }
            return self::FAILURE;
        }

        $this->info('Backup creado. Aplicando retención...');

        $files = collect(glob(storage_path('app/'.$dir.'/*.sql.gz')))
            ->map(fn($p)=>basename($p))
            ->sortDesc()
            ->values();

        if ($files->count() > $keep) {
            $toDelete = $files->slice($keep); // older ones beyond limit
            foreach ($toDelete as $file) {
                $full = storage_path('app/'.$dir.'/'.$file);
                if (is_file($full)) {
                    @unlink($full);
                    $this->line("Eliminado antiguo: {$file}");
                }
            }
        }

    $finalCount = count(glob(storage_path('app/'.$dir.'/*.sql.gz')));
    $this->info('Retención aplicada. Total backups: '.$finalCount);
        return self::SUCCESS;
    }
}
