<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LegacyImport extends Command
{
    protected $signature = 'legacy:import
        {--batch=500 : Tamaño de lote}
        {--start-id=0 : ID inicial para reanudar}
        {--pretend : Solo muestra conteos / no inserta}
        {--tables= : Coma separada para limitar (users,products,...)}';

    protected $description = 'Importa datos desde la conexión legacy adaptándolos al esquema actual.';

    public function handle(): int
    {
        $conn = 'legacy';
        // Probar conexión
        try { DB::connection($conn)->getPdo(); } catch(\Throwable $e){ $this->error('No conecta legacy: '.$e->getMessage()); return self::FAILURE; }

        $limitTables = $this->option('tables') ? collect(explode(',', $this->option('tables')))->map(fn($t)=>trim($t))->filter() : null;
        $pretend = $this->option('pretend');

        // Definir orden de importación para FK
            $configTables = config('legacy_import.tables', []);
            $plan = [];
            foreach($configTables as $table => $meta){
                $plan[$table] = fn()=>$this->importGeneric($conn, $table, $meta, $pretend);
            }

        foreach($plan as $name=>$fn){
            if($limitTables && !$limitTables->contains($name)) continue;
            $this->components->info("Importando {$name}...");
            $fn();
        }
        $this->info('Proceso finalizado');
        return self::SUCCESS;
    }

    protected function importGeneric(string $legacy, string $table, array $meta, bool $pretend): void
        {
            if(!DB::connection($legacy)->getSchemaBuilder()->hasTable($table)){
                $this->warn("Tabla legacy no existe: {$table}");
                return;
            }
            $rows = DB::connection($legacy)->table($table)->get();
            $this->line("Legacy {$table}: ".$rows->count());
            if($pretend) return;

            $matchCols = $meta['match'] ?? [];
            $map = $meta['map'] ?? [];

            foreach($rows as $r){
                $match = [];
                foreach($matchCols as $mc){ $match[$mc] = $r->{$mc} ?? null; }
                $data = [];
                foreach($map as $dest=>$spec){
                    // spec puede ser string (col legacy) o array [col legacy, transform]
                    $col = $spec; $transform = null;
                    if(is_array($spec)){ [$col,$transform] = $spec; }
                    $val = $r->{$col} ?? null;
                    if($transform){
                        $val = $this->applyTransform($transform, $val, $r, $table, $dest);
                    }
                    // Campos de timestamps fallback
                    if(in_array($dest,['created_at','updated_at']) && !$val){ $val = now(); }
                    $data[$dest] = $val;
                }
                DB::table($table)->updateOrInsert($match, $data);
            }
        }

    protected function applyTransform(string $name, $value, $row, string $table, string $dest){
            return match($name){
                'hash_if_plain' => $this->transformHashIfPlain($value),
                'passthrough' => $value,
                default => $value,
            };
        }

    protected function transformHashIfPlain($value){
            if(!$value) return \Illuminate\Support\Str::random(40);
            if(is_string($value) && \Illuminate\Support\Str::startsWith($value, ['$2y$','$2a$']) && strlen($value)===60) return $value;
            return Hash::make($value);
        }
}
