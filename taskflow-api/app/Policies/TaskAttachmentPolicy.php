<?php

namespace App\Policies;

use App\Models\TaskAttachment;
use App\Models\User;

class TaskAttachmentPolicy
{
    private function getProjectRole(User $user, TaskAttachment $attachment): ?string
    {
        $member = $attachment->task->project->members()
            ->where('users.id', $user->id)
            ->first();

        return $member?->pivot?->role;
    }

    public function delete(User $user, TaskAttachment $attachment): bool
    {
        $role = $this->getProjectRole($user, $attachment);

        return $attachment->user_id === $user->id || $role === 'owner';
    }
}