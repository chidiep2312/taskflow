<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Interfaces\ProjectMemberRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectMemberService
{
    public function __construct(
        protected ProjectMemberRepositoryInterface $projectMemberRepository,
        protected ActivityLogService $activityLogService
    ) {}

    public function getMembers(Project $project): Collection
    {
        return $this->projectMemberRepository->getMembers($project);
    }

    public function addMember(User $actor, Project $project, array $data): void
    {
        DB::transaction(function () use ($actor, $project, $data) {
            $user = User::where('email', $data['email'])->firstOrFail();

            if ($this->projectMemberRepository->isMember($project, $user)) {
                throw ValidationException::withMessages([
                    'email' => ['User is already a member of this project.'],
                ]);
            }

            $this->projectMemberRepository->addMember(
                $project,
                $user,
                $data['role']
            );

            $this->activityLogService->log(
                $actor,
                'project_member_added',
                $project,
                [
                    'member_email' => $user->email,
                    'role' => $data['role'],
                ]
            );
        });
    }

    public function updateRole(User $actor, Project $project, User $member, string $role): void
    {
        DB::transaction(function () use ($actor, $project, $member, $role) {
            $currentRole = $this->projectMemberRepository->getRole($project, $member);

            if ($currentRole === 'owner') {
                throw ValidationException::withMessages([
                    'member' => ['Cannot change owner role.'],
                ]);
            }

            if (!$currentRole) {
                throw ValidationException::withMessages([
                    'member' => ['User is not a member of this project.'],
                ]);
            }

            $this->projectMemberRepository->updateRole($project, $member, $role);

            $this->activityLogService->log(
                $actor,
                'project_member_role_updated',
                $project,
                [
                    'member_email' => $member->email,
                    'from' => $currentRole,
                    'to' => $role,
                ]
            );
        });
    }

    public function removeMember(User $actor, Project $project, User $member): void
    {
        DB::transaction(function () use ($actor, $project, $member) {
            $currentRole = $this->projectMemberRepository->getRole($project, $member);

            if ($currentRole === 'owner') {
                throw ValidationException::withMessages([
                    'member' => ['Cannot remove project owner.'],
                ]);
            }

            if (!$currentRole) {
                throw ValidationException::withMessages([
                    'member' => ['User is not a member of this project.'],
                ]);
            }

            $this->projectMemberRepository->removeMember($project, $member);

            $this->activityLogService->log(
                $actor,
                'project_member_removed',
                $project,
                [
                    'member_email' => $member->email,
                ]
            );
        });
    }
}