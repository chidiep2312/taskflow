<?php

namespace App\Services;

use App\Services\ActivityLogService;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository,
        protected ActivityLogService $activityLogService

    ) {}

    public function getUserProjects(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->getUserProjects($user, $filters);
    }

    public function getProject(int $id): ?Project
    {
        return $this->projectRepository->findById($id);
    }

    public function createProject(User $user, array $data): Project
    {
        return DB::transaction(function () use ($user, $data) {
            $data['user_id'] = $user->id;

            $project = $this->projectRepository->create($data);

            $this->activityLogService->log(
                $user,
                'project_created',
                $project,
                $data
            );
            $project->members()->attach($user->id, [
                'role' => 'owner',
            ]);

            return $project;
        });
    }

    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            return $this->projectRepository->update($project, $data);
        });
    }

    public function deleteProject(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            return $this->projectRepository->delete($project);
        });
    }
}
