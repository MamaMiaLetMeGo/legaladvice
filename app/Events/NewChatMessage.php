<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return new PrivateChannel('chat.conversation.' . $this->message->conversation_id);
    }

    // Your existing broadcastWith is good, but let's add more security
    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'user_id' => $this->message->user_id,
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'is_lawyer' => $this->message->user->is_lawyer
                ],
                'created_at' => $this->message->created_at
            ]
        ];
    }
}
