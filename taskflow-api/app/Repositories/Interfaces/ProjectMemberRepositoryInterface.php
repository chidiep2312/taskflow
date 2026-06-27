<?php

namespace App\Repositories\Interfaces;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;

interface ProjectMemberRepositoryInterface
{
    public function getMembers(Project $project): Collection;

    public function addMember(Project $project, User $user, string $role): void;

    public function updateRole(Project $project, User $user, string $role): void;

    public function removeMember(Project $project, User $user): void;

    public function isMember(Project $project, User $user): bool;

    public function getRole(Project $project, User $user): ?string;
}