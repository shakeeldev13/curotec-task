<?php

namespace App\Http\Controllers;

use App\Events\TaskEvent;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index()
    {
        return Task::latest()->get();
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(TaskRequest $request)
    {
        $task = Task::create($request->validated());
        Log::info('Broadcasting task created event', [
            'task' => $task->id,
            'task_data' => $task->toArray(),
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_config' => config('broadcasting.connections.pusher')
        ]);
        event(new TaskEvent($task, 'created'));
        return response()->json($task, 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        return $task;
    }

    /**
     * Update the specified task in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        Log::info('Broadcasting task updated event', [
            'task' => $task->id,
            'task_data' => $task->toArray(),
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_config' => config('broadcasting.connections.pusher')
        ]);
        event(new TaskEvent($task, 'updated'));
        return response()->json($task);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        Log::info('Broadcasting task deleted event', [
            'task' => $task->id,
            'task_data' => $task->toArray(),
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_config' => config('broadcasting.connections.pusher')
        ]);
        event(new TaskEvent($task, 'deleted'));
        return response()->json(null, 204);
    }
}
