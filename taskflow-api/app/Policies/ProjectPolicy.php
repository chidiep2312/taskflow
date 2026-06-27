<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    private function getRole(User $user, Project $project): ?string
    {
        $member = $project->members()
            ->where('users.id', $user->id)
            ->first();

        return $member?->pivot?->role;
    }

    public function view(User $user, Project $project): bool
    {
        return $this->getRole($user, $project) !== null;
    }

    public function update(User $user, Project $project): bool
    {
        return $this->getRole($user, $project) === 'owner';
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->getRole($user, $project) === 'owner';
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return $this->getRole($user, $project) === 'owner';
    }

    public function manageTasks(User $user, Project $project): bool
    {
        return in_array($this->getRole($user, $project), ['owner', 'member']);
    }
}