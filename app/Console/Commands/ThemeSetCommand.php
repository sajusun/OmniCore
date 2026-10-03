<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ThemeSetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:set {theme? : Key of the theme to activate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set and activate a UI theme in .env (modern_indigo, glassmorphism, dark_luxury, minimalist_clean, corporate_blue)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var array<string, array{name: string, category: string, accent: string, mode: string, description: string}> $themes */
        $themes = (array) config('theme.themes', []);
        $availableKeys = array_keys($themes);

        $selectedTheme = $this->argument('theme');

        if (!$selectedTheme) {
            $selectedTheme = $this->choice(
                'Select a dashboard UI theme to activate:',
                $availableKeys,
                0
            );
        }

        $selectedTheme = strtolower(trim((string) $selectedTheme));

        if (!in_array($selectedTheme, $availableKeys, true)) {
            $this->error("Invalid theme '{$selectedTheme}'. Available themes: " . implode(', ', $availableKeys));
            return Command::FAILURE;
        }

        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            $this->error('.env file not found.');
            return Command::FAILURE;
        }

        $envContent = (string) File::get($envPath);

        if (preg_match('/^APP_THEME=.*$/m', $envContent)) {
            $envContent = (string) preg_replace('/^APP_THEME=.*$/m', "APP_THEME={$selectedTheme}", $envContent);
        } else {
            $envContent .= "\nAPP_THEME={$selectedTheme}\n";
        }

        File::put($envPath, $envContent);

        // Clear config cache so the new env takes immediate effect
        $this->call('config:clear');

        $themeData = $themes[$selectedTheme] ?? [];
        $themeName = $themeData['name'] ?? $selectedTheme;

        $this->info("✨ Successfully activated UI theme: [{$themeName}] ({$selectedTheme})");
        $this->line("Accent: <comment>" . ($themeData['accent'] ?? '') . "</comment> | Mode: <comment>" . ($themeData['mode'] ?? '') . "</comment>");
        $this->line("Reload the dashboard in your browser to experience the new aesthetic.\n");

        return Command::SUCCESS;
    }
}
