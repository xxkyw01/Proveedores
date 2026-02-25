<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean {--days=30 : Días a conservar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina archivos de logs en storage/logs más antiguos que X días';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $path = storage_path('logs');

        if (! File::exists($path)) {
            $this->info('No existe la carpeta de logs: ' . $path);
            return 0;
        }

        $threshold = now()->subDays($days)->getTimestamp();
        $files = File::files($path);
        $deleted = 0;

        foreach ($files as $file) {
            if ($file->getMTime() < $threshold) {
                if (in_array($file->getFilename(), ['.gitignore'])) {
                    continue;
                }
                File::delete($file->getPathname());
                $this->info('Borrado: ' . $file->getFilename());
                $deleted++;
            }
        }

        $this->info("Total archivos borrados: $deleted");
        return 0;
    }
}
