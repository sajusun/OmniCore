<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ThemeListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available UI themes and their current active status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $activeTheme = (string) config('theme.active', env('APP_THEME', 'modern_indigo'));
        /** @var array<string, array{name: string, category: string, accent: string, mode: string, description: string}> $themes */
        $themes = (array) config('theme.themes', []);

        $rows = [];
        foreach ($themes as $key => $theme) {
            $isActive = $key === $activeTheme;
            $rows[] = [
                $isActive ? "<info>✔ {$key}</info>" : $key,
                $theme['name'] ?? '',
                $theme['category'] ?? '',
                $theme['accent'] ?? '',
                $theme['mode'] ?? '',
                $isActive ? '<info>ACTIVE</info>' : 'Available',
            ];
        }

        $this->info("🎨 OmniCore Multi-Theme Engine");
        $this->table(['Key', 'Theme Name', 'Category', 'Accent', 'Mode', 'Status'], $rows);
        $this->line("Active theme in .env: <comment>{$activeTheme}</comment>");
        $this->line("To switch theme, run: <comment>php artisan theme:set {theme_key}</comment>\n");

        return Command::SUCCESS;
    }
}
