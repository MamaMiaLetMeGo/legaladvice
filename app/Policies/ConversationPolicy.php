<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConversationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        // User can view if they are the conversation owner or the assigned lawyer
        return $user->id === $conversation->user_id || 
               $user->id === $conversation->lawyer_id ||
               ($user->is_lawyer && $conversation->status === 'pending');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Conversation $conversation): bool
    {
        // Only the assigned lawyer can update the conversation
        return $user->id === $conversation->lawyer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Conversation $conversation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Conversation $conversation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Conversation $conversation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can claim the conversation.
     */
    public function claim(User $user, Conversation $conversation): bool
    {
        // Only lawyers can claim pending conversations
        return $user->is_lawyer && 
               $conversation->status === 'pending' && 
               !$conversation->lawyer_id;
    }

    /**
     * Determine whether the user can send a message to the conversation.
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        // Users can send messages if they own the conversation or are the assigned lawyer
        return $user->id === $conversation->user_id || 
               $user->id === $conversation->lawyer_id;
    }
}
