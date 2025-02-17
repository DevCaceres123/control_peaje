<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CorteDeTurno;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Schedule $schedule): void
    {
        $schedule->command(CorteDeTurno::class)->dailyAt('23:59');

    }
}
