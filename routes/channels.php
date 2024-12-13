Broadcast::channel('conversations', function ($user) {
    return $user->is_lawyer;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);
    return $user->id === $conversation->user_id || 
           $user->id === $conversation->lawyer_id ||
           ($user->is_lawyer && $conversation->status === 'pending');
}); 