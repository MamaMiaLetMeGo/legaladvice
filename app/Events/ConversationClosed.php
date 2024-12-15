<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationClosed implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return new PrivateChannel('conversations');
    }

    public function broadcastWith()
    {
        return [
            'conversation' => [
                'id' => $this->conversation->id,
                'status' => 'closed',
                'closed_at' => $this->conversation->updated_at
            ]
        ];
    }
}