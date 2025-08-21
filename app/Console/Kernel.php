<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Ejecutar a las 18:00 hora Guatemala (America/Guatemala)
        $schedule->command('db:backup --keep=4')->timezone('America/Guatemala')->dailyAt('18:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
