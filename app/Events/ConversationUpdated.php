<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Conversation;

class ConversationUpdated implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return new PrivateChannel('chat.conversation.' . $this->conversation->id);
    }

    public function broadcastWith()
    {
        return [
            'conversation' => [
                'id' => $this->conversation->id,
                'status' => $this->conversation->status,
                'lawyer_id' => $this->conversation->lawyer_id,
                'updated_at' => $this->conversation->updated_at
            ]
        ];
    }
}