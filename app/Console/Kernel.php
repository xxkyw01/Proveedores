<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Limpiar logs antiguos una vez al día
        $schedule->command('logs:clean')->daily();
    }

    /**
     * Register the commands for the application.
     */
    
     // app/Console/Kernel.php

protected function commands(): void
{
    $this->load(__DIR__.'/Commands');

    require base_path('routes/console.php');

  //  $this->commands([
      //  \App\Console\Commands\EncriptarProveedores::class,
    //]);
}

}
