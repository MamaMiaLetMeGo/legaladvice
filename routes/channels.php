<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

// Channel for lawyers to receive new conversation notifications
Broadcast::channel('conversations', function ($user) {
    // Only allow lawyers to subscribe to this channel
    return $user->is_lawyer;
});

// Channel for individual chat conversations
Broadcast::channel('chat.conversation.{conversationId}', function ($user, $conversationId) {
    // Find the conversation, but handle the case where it might not exist
    $conversation = Conversation::find($conversationId);
    
    // If conversation doesn't exist, deny access
    if (!$conversation) {
        return false;
    }
    
    // Allow access if any of these conditions are met:
    // 1. The user is the client who created the conversation
    // 2. The user is the assigned lawyer
    // 3. The user is a lawyer and the conversation is pending assignment
    return $user->id === $conversation->user_id || 
           $user->id === $conversation->lawyer_id ||
           ($user->is_lawyer && $conversation->status === 'pending');
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