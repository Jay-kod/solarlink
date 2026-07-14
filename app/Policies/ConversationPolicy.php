<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Bypasses policy checks globally for Admins.
     */
    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * Determine whether the user can view the conversation.
     */
    public function view(User $user, Conversation $conversation)
    {
        return $conversation->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can send messages.
     */
    public function sendMessage(User $user, Conversation $conversation)
    {
        // Concluded chats cannot receive new messages
        if ($conversation->status === 'concluded') {
            return false;
        }

        return $conversation->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can manage (conclude or delete) the conversation.
     */
    public function manageLifecycle(User $user, Conversation $conversation)
    {
        // Only Admin or Customer (asset owner) can conclude or delete the group chat
        return $user->role === 'admin' || $user->role === 'customer';
    }
}
