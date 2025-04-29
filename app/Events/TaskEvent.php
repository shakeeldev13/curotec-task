<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TaskEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, string $action)
    {
        $this->task = $task;
        $this->action = $action;
        Log::info('TaskEvent constructed', [
            'task_id' => $task->id,
            'action' => $action,
            'task_title' => $task->title,
            'task_status' => $task->status
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting on channel: tasks', [
            'task_id' => $this->task->id,
            'action' => $this->action,
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_config' => config('broadcasting.connections.pusher'),
            'channel_name' => 'tasks'
        ]);
        return new Channel('tasks');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith()
    {
        $data = [
            'task' => $this->task,
            'action' => $this->action
        ];
        Log::info('Broadcasting data', [
            'task_id' => $this->task->id,
            'action' => $this->action,
            'data' => $data,
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_config' => config('broadcasting.connections.pusher')
        ]);
        return $data;
    }

    /**
     * Get the broadcast event name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        $eventName = match($this->action) {
            'created' => 'task-created',
            'updated' => 'task-updated',
            'deleted' => 'task-deleted',
            default => 'task-event'
        };
        
        Log::info('Broadcasting event name', [
            'task_id' => $this->task->id,
            'action' => $this->action,
            'event_name' => $eventName,
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_config' => config('broadcasting.connections.pusher')
        ]);
        
        return $eventName;
    }
} 