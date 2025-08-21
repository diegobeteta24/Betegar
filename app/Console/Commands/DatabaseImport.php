<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class DatabaseImport extends Command
{
    protected $signature = 'db:import {dump : Ruta al archivo .sql o .sql.gz} {--drop : Elimina todas las tablas antes de importar} {--no-migrate : No ejecutar migraciones después}';
    protected $description = 'Importa un volcado SQL (opcional .gz) reemplazando la base actual.';

    public function handle(): int
    {
        $dump = $this->argument('dump');
        if (!is_file($dump)) {
            $this->error("Archivo no encontrado: $dump");
            return self::FAILURE;
        }
        $ext = pathinfo($dump, PATHINFO_EXTENSION);

        $conn = config('database.connections.'.config('database.default'));
        if (!in_array($conn['driver'], ['mysql','mariadb'])) {
            $this->error('Sólo implementado para MySQL/MariaDB');
            return self::FAILURE;
        }

        $host = $conn['host'];
        $port = $conn['port'];
        $db   = $conn['database'];
        $user = $conn['username'];
        $pass = $conn['password'];

        if ($this->option('drop')) {
            $this->warn('Eliminando tablas existentes...');
            $tables = DB::select("SHOW TABLES");
            $key = 'Tables_in_'.$db;
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach ($tables as $t) {
                $name = $t->$key;
                DB::statement("DROP TABLE IF EXISTS `{$name}`");
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('Tablas eliminadas.');
        }

        $this->info('Importando dump...');
        $cat = $ext === 'gz' ? "gzip -dc ".escapeshellarg($dump) : 'cat '.escapeshellarg($dump);
        $cmd = sprintf('%s | mysql -h%s -P%s -u%s %s %s',
            $cat,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            $pass ? ('-p'.escapeshellarg($pass)) : '',
            escapeshellarg($db)
        );
        $process = Process::fromShellCommandline($cmd, base_path(), null, null, 0);
        $process->run(function($type,$buffer){ $this->output->write($buffer); });
        if (!$process->isSuccessful()) {
            $this->error('Fallo importación: '.$process->getErrorOutput());
            return self::FAILURE;
        }
        $this->info('Importación completada.');

        if(!$this->option('no-migrate')){
            $this->info('Ejecutando migraciones pendientes...');
            $this->call('migrate', ['--force'=>true]);
        }
        $this->info('Listo.');
        return self::SUCCESS;
    }
}
