<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create tasks with different statuses and priorities
        Task::factory()->create([
            'title' => 'Complete Project Documentation',
            'description' => 'Write comprehensive documentation for the project',
            'status' => 'pending',
            'priority' => 3,
            'due_date' => now()->addDays(5),
        ]);

        Task::factory()->create([
            'title' => 'Fix Bug in Authentication',
            'description' => 'Investigate and fix the authentication bug',
            'status' => 'in_progress',
            'priority' => 4,
            'due_date' => now()->addDays(2),
        ]);

        Task::factory()->create([
            'title' => 'Implement New Feature',
            'description' => 'Add the new feature as per requirements',
            'status' => 'completed',
            'priority' => 2,
            'due_date' => now()->subDays(1),
        ]);

        Task::factory()->create([
            'title' => 'Code Review',
            'description' => 'Review the latest pull requests',
            'status' => 'pending',
            'priority' => 1,
            'due_date' => null,
        ]);

        // Create some random tasks
        Task::factory()->count(10)->create();
    }
}
