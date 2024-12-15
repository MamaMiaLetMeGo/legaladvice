<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

// Channel for lawyers to receive new conversation notifications
Broadcast::channel('conversations', function ($user) {
    // Only allow lawyers to subscribe to this channel
    return $user->is_lawyer;
});

// Channel for individual chat conversations
Broadcast::channel('chat.conversation.{id}', function ($user = null, $id) {
    // Allow access even without authentication
    $conversation = \App\Models\Conversation::find($id);
    
    if (!$user) {
        // For guest users, check if the conversation exists and is public
        return $conversation && !$conversation->user_id;
    }
    
    // For authenticated users, check if they're part of the conversation
    return $conversation && (
        $conversation->user_id === $user->id || 
        $conversation->lawyer_id === $user->id
    );
});

// Optional: Add a presence channel for typing indicators or online status
Broadcast::channel('presence.chat.conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }
    
    // If user has access to the conversation, return their info for the presence channel
    if ($user->id === $conversation->user_id || 
        $user->id === $conversation->lawyer_id || 
        ($user->is_lawyer && $conversation->status === 'pending')) {
        
        return [
            'id' => $user->id,
            'name' => $user->name,
            'is_lawyer' => $user->is_lawyer
        ];
    }
    
    return false;
});