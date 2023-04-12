<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->exec('touch token_schedule')->everyMinute()
            ->when(function () {
                return true;
            });

        $schedule->command('smm:delete')->daily()
            ->when(function () {
                return boolval(env('AUTO_DELETE'));
            });

        $schedule->command('smm:check')->everyMinute()
            ->when(function() {
                return boolval(env('AUTO_CHECK'));
            });

        $schedule->exec('php artisan smm:restart restart_all &')->everyTenMinutes()
            ->when(function() {
                return boolval(env('AUTO_RESTART'));
            });

        $schedule->exec('php artisan z:update master &')->everyMinute()
            ->when(function () {
                return boolval(env('AUTO_UPDATE'));
            });

        $schedule->command('updateConnectumTransactions')->everyMinute();
        $schedule->command('cu_capi')->twiceDaily(0, 12);

        $schedule->command('smm:logins_to_google_sheets')
            ->everyFourHours()
            ->when(function () {
                return boolval(config('services.login_stats.enabled'));
            });

        $schedule->command('smm:check_sendpulse_events')->everyMinute()
            ->when(function () {
                return boolval(config('services.sendpulse.enabled'));
            });

        $schedule->command('currencies:update')->twiceDaily(0, 12);

        $schedule->command('psp:updatePayToDayTransactions')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
