<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:list {--detail : Show extended module statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered dashboard modules and their operational status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $modulesPath = app_path('Modules');

        if (!File::isDirectory($modulesPath)) {
            $this->error('Modules directory not found at: ' . $modulesPath);
            return Command::FAILURE;
        }

        $directories = File::directories($modulesPath);

        if (empty($directories)) {
            $this->warn('No modules found in ' . $modulesPath);
            return Command::SUCCESS;
        }

        $rows = [];
        $totalModules = count($directories);
        $totalMigrations = 0;

        foreach ($directories as $dir) {
            $name = basename($dir);

            // Provider check
            $providerFile = "{$dir}/Providers/{$name}ServiceProvider.php";
            $hasProvider = File::exists($providerFile);

            // Routes check
            $apiRoutes = File::exists("{$dir}/Routes/api.php");
            $webRoutes = File::exists("{$dir}/Routes/web.php") || File::exists("{$dir}/Routes/admin.php");
            $routeInfo = [];
            if ($apiRoutes) $routeInfo[] = 'API';
            if ($webRoutes) $routeInfo[] = 'Web/Admin';
            $routesStr = empty($routeInfo) ? 'None' : implode(', ', $routeInfo);

            // Migrations check
            $migrationsDir = "{$dir}/Database/Migrations";
            $migrationCount = File::isDirectory($migrationsDir) ? count(File::files($migrationsDir)) : 0;
            $totalMigrations += $migrationCount;

            // Events check
            $eventsDir = "{$dir}/Events";
            $eventCount = File::isDirectory($eventsDir) ? count(File::files($eventsDir)) : 0;

            // Docs check
            $hasDoc = File::exists("{$dir}/USAGE.md") || File::exists("{$dir}/README.md");

            $rows[] = [
                'name'        => $name,
                'provider'    => $hasProvider ? '✓ Active' : '✗ Missing',
                'routes'      => $routesStr,
                'migrations'  => $migrationCount > 0 ? "{$migrationCount} file(s)" : '0',
                'events'      => $eventCount > 0 ? "{$eventCount} event(s)" : 'None',
                'docs'        => $hasDoc ? '✓ USAGE.md' : '✗ Missing',
            ];
        }

        $this->newLine();
        $this->info(" OmniCore Dashboard Modules Overview (Total: {$totalModules} Modules, {$totalMigrations} Migrations)");
        $this->newLine();

        $this->table(
            ['Module', 'Provider Status', 'Routes', 'Migrations', 'Events', 'Documentation'],
            $rows
        );

        $this->newLine();
        $this->line(' Tip: Run <comment>php artisan module:make <Name></comment> to scaffold a new plug-and-play module.');
        $this->newLine();

        return Command::SUCCESS;
    }
}
