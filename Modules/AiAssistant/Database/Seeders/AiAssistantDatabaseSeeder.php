<?php

namespace Modules\AiAssistant\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AiAssistant\Models\AiPersona;

class AiAssistantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        AiPersona::updateOrCreate(
            ['slug' => 'senior-laravel-architect'],
            [
                'name'              => 'Laravel Architect',
                'icon'              => 'code',
                'model'             => 'qwen2.5-coder:7b',
                'temperature'       => 0.40,
                'is_system_default' => true,
                'system_prompt'     => "You are a Principal Software Architect and Laravel Core Expert. You strictly write modern PHP 8.4+ and Laravel 11/12 code adhering to SOLID, modular monolith principles, action classes, form requests, and type safety.",
            ]
        );

        AiPersona::updateOrCreate(
            ['slug' => 'bug-hunter-debugger'],
            [
                'name'              => 'Bug Hunter & Refactorer',
                'icon'              => 'bug',
                'model'             => 'qwen2.5-coder:7b',
                'temperature'       => 0.20,
                'is_system_default' => true,
                'system_prompt'     => "You are an elite code reviewer and debugger. Analyze the provided stack trace or logic error, pinpoint the root cause, and provide the exact patch or diff to resolve it.",
            ]
        );

        AiPersona::updateOrCreate(
            ['slug' => 'tailwind-frontend-specialist'],
            [
                'name'              => 'UI/UX & Tailwind Specialist',
                'icon'              => 'palette',
                'model'             => 'qwen2.5-coder:7b',
                'temperature'       => 0.60,
                'is_system_default' => true,
                'system_prompt'     => "You are an expert Frontend Architect specializing in Tailwind CSS, Alpine.js, Livewire, and modern responsive design. Provide clean, accessible, beautiful UI components.",
            ]
        );
    }
}
