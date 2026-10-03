<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ThemeEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // Restore default theme in .env if changed
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $content = (string) File::get($envPath);
            $content = (string) preg_replace('/^APP_THEME=.*$/m', 'APP_THEME=modern_indigo', $content);
            File::put($envPath, $content);
        }

        parent::tearDown();
    }

    public function test_all_five_themes_are_registered_in_config(): void
    {
        $themes = config('theme.themes');

        $this->assertIsArray($themes);
        $this->assertCount(5, $themes);
        $this->assertArrayHasKey('modern_indigo', $themes);
        $this->assertArrayHasKey('glassmorphism', $themes);
        $this->assertArrayHasKey('dark_luxury', $themes);
        $this->assertArrayHasKey('minimalist_clean', $themes);
        $this->assertArrayHasKey('corporate_blue', $themes);
    }

    public function test_theme_list_artisan_command_displays_registered_themes(): void
    {
        $this->artisan('theme:list')
            ->expectsOutputToContain('Modern Indigo')
            ->expectsOutputToContain('Glassmorphism Frosted')
            ->expectsOutputToContain('Dark Luxury OLED')
            ->expectsOutputToContain('Minimalist Clean')
            ->expectsOutputToContain('Corporate Blue & Slate')
            ->assertSuccessful();
    }

    public function test_theme_set_artisan_command_validates_and_switches_theme(): void
    {
        $this->artisan('theme:set invalid_theme')
            ->expectsOutputToContain("Invalid theme 'invalid_theme'")
            ->assertFailed();

        $this->artisan('theme:set dark_luxury')
            ->expectsOutputToContain('Successfully activated UI theme: [Dark Luxury OLED] (dark_luxury)')
            ->assertSuccessful();
    }

    public function test_dashboard_renders_theme_attributes(): void
    {
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole($role);
        $this->actingAs($user);

        config(['theme.active' => 'glassmorphism']);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('data-theme="glassmorphism"', false);
        $response->assertSee('theme-glassmorphism', false);
    }
}
