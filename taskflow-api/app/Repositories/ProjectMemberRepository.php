<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Interfaces\ProjectMemberRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectMemberRepository implements ProjectMemberRepositoryInterface
{
    public function getMembers(Project $project): Collection
    {
        return $project->members()
            ->select('users.id', 'users.name', 'users.email')
            ->get();
    }

    public function addMember(Project $project, User $user, string $role): void
    {
        $project->members()->attach($user->id, [
            'role' => $role,
        ]);
    }

    public function updateRole(Project $project, User $user, string $role): void
    {
        $project->members()->updateExistingPivot($user->id, [
            'role' => $role,
        ]);
    }

    public function removeMember(Project $project, User $user): void
    {
        $project->members()->detach($user->id);
    }

    public function isMember(Project $project, User $user): bool
    {
        return $project->members()
            ->where('users.id', $user->id)
            ->exists();
    }

    public function getRole(Project $project, User $user): ?string
    {
        $member = $project->members()
            ->where('users.id', $user->id)
            ->first();

        return $member?->pivot?->role;
    }
}