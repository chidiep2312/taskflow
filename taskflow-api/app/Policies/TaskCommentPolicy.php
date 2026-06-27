<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;

class TaskCommentPolicy
{
    private function getProjectRole(User $user, TaskComment $comment): ?string
    {
        $member = $comment->task->project->members()
            ->where('users.id', $user->id)
            ->first();

        return $member?->pivot?->role;
    }

    public function delete(User $user, TaskComment $comment): bool
    {
        $role = $this->getProjectRole($user, $comment);

        return $comment->user_id === $user->id || $role === 'owner';
    }
}