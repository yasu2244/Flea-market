<?php

namespace App\Policies;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChatMessagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ChatMessage $chatMessage): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ChatMessage $message): bool
    {
        // 自分が投稿したメッセージのみ編集可能
        return $user->id === $message->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChatMessage $message)
    {
        // 自分のメッセージだけ削除可
        return $user->id === $message->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ChatMessage $chatMessage): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ChatMessage $chatMessage): bool
    {
        //
    }
}
