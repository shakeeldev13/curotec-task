<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertJson;
use function Pest\Laravel\assertJsonStructure;
use function Pest\Laravel\assertJsonValidationErrors;
use function Pest\Laravel\assertStatus;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a test user for authenticated routes
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('can fetch all tasks', function () {
    // Create test tasks
    $tasks = Task::factory()->count(3)->create();

    // Make API request
    $response = getJson('/api/tasks');

    // Assert response
    $response->assertStatus(200)
        ->assertJsonCount(3)
        ->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'description',
                'status',
                'priority',
                'due_date',
                'created_at',
                'updated_at'
            ]
        ]);
});

it('can create a new task', function () {
    // Task data
    $taskData = [
        'title' => 'Test Task',
        'description' => 'This is a test task',
        'status' => 'pending',
        'priority' => 3,
        'due_date' => now()->addDays(7)->format('Y-m-d')
    ];

    // Make API request
    $response = postJson('/api/tasks', $taskData);

    // Assert response
    $response->assertStatus(201)
        ->assertJson($taskData)
        ->assertJsonStructure([
            'id',
            'title',
            'description',
            'status',
            'priority',
            'due_date',
            'created_at',
            'updated_at'
        ]);

    // Assert database
    assertDatabaseHas('tasks', $taskData);
});

it('validates required fields when creating task', function () {
    // Make API request with missing required fields
    $response = postJson('/api/tasks', []);

    // Assert validation errors
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'status', 'priority']);
});

it('validates field types when creating task', function () {
    // Invalid task data
    $invalidData = [
        'title' => 123, // Should be string
        'status' => 'invalid_status', // Should be one of: pending, in_progress, completed
        'priority' => 'high', // Should be integer
        'due_date' => 'invalid_date' // Should be valid date
    ];

    // Make API request
    $response = postJson('/api/tasks', $invalidData);

    // Assert validation errors
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status', 'priority', 'due_date']);
});

it('can fetch a single task', function () {
    // Create test task with a due date
    $task = Task::factory()->create([
        'due_date' => now()->addDays(7)
    ]);

    // Make API request
    $response = getJson("/api/tasks/{$task->id}");

    // Assert response
    $response->assertStatus(200)
        ->assertJson([
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'due_date' => $task->due_date->format('Y-m-d')
        ]);
});

it('can fetch a task with null due date', function () {
    // Create test task without due date
    $task = Task::factory()->create([
        'due_date' => null
    ]);

    // Make API request
    $response = getJson("/api/tasks/{$task->id}");

    // Assert response
    $response->assertStatus(200)
        ->assertJson([
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'due_date' => null
        ]);
});

it('returns 404 when task not found', function () {
    // Make API request with non-existent ID
    $response = getJson('/api/tasks/999');

    // Assert response
    $response->assertStatus(404);
});

it('can update an existing task', function () {
    // Create test task
    $task = Task::factory()->create();

    // Update data
    $updateData = [
        'title' => 'Updated Task Title',
        'description' => 'Updated task description',
        'status' => 'in_progress',
        'priority' => 4,
        'due_date' => now()->addDays(14)->format('Y-m-d')
    ];

    // Make API request
    $response = putJson("/api/tasks/{$task->id}", $updateData);

    // Assert response
    $response->assertStatus(200)
        ->assertJsonFragment($updateData);

    // Assert database
    assertDatabaseHas('tasks', $updateData);
});

it('validates fields when updating task', function () {
    // Create test task
    $task = Task::factory()->create();

    // Invalid update data
    $invalidData = [
        'title' => '', // Empty title
        'status' => 'invalid_status',
        'priority' => 10, // Out of range
        'due_date' => 'invalid_date'
    ];

    // Make API request
    $response = putJson("/api/tasks/{$task->id}", $invalidData);

    // Assert validation errors
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'status', 'priority', 'due_date']);
});

it('can delete a task', function () {
    // Create test task
    $task = Task::factory()->create();

    // Make API request
    $response = deleteJson("/api/tasks/{$task->id}");

    // Assert response
    $response->assertStatus(204);

    // Assert database - check for soft delete
    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
});

it('returns 404 when deleting non-existent task', function () {
    // Make API request with non-existent ID
    $response = deleteJson('/api/tasks/999');

    // Assert response
    $response->assertStatus(404);
}); 