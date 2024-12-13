<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_name' => $this->message->user->name,
            'content' => substr($this->message->content, 0, 50) . '...',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_name' => $this->message->user->name,
            'content' => substr($this->message->content, 0, 50) . '...',
        ]);
    }
} 