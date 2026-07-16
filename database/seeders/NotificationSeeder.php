<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notification::query()->truncate();

        $users = User::query()->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping notification seeding.');
            return;
        }

        $notificationTemplates = [
            [
                'type' => 'info',
                'title' => 'Welcome to OJais Docs',
                'body' => 'Thanks for joining. Take a look at your dashboard to get started.',
            ],
            [
                'type' => 'success',
                'title' => 'Your profile was updated',
                'body' => 'The latest changes to your profile have been saved successfully.',
            ],
            [
                'type' => 'warning',
                'title' => 'Action needed',
                'body' => 'Please review your account settings before continuing.',
            ],
            [
                'type' => 'general',
                'title' => 'New announcement',
                'body' => 'A new feature has been released for all users.',
            ],
        ];

        foreach ($users as $user) {
            foreach ($notificationTemplates as $index => $template) {
                Notification::create([
                    'type' => $template['type'],
                    'notifiable_type' => User::class,
                    'notifiable_id' => $user->id,
                    'title' => $template['title'],
                    'body' => $template['body'],
                    'read_at' => $index % 2 === 0 ? null : now()->subDay($index + 1),
                ]);
            }
        }

        $this->command->info('Notifications seeded successfully.');
    }
}
