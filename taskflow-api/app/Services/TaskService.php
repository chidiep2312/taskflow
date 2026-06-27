<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;
class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository,
        protected ActivityLogService $activityLogService
        ) {}

    public function getProjectTasks(Project $project, array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->getProjectTasks($project, $filters);
    }

    public function createTask(Project $project, array $data): Task
    {
        return DB::transaction(function () use ($project, $data) {
            $data['project_id'] = $project->id;

            $task = $this->taskRepository->create($data);

             $this->activityLogService->log(
               $project->user,
               'task_created',
               $task,
            [
                'title' => $task->title,
                'project_id' => $project->id,
            ]
        );

        return $task;
        });
    }

public function updateTask(Task $task, array $data): Task
{
    return DB::transaction(function () use ($task, $data) {
        $oldStatus = $task->status;

        $updatedTask = $this->taskRepository->update($task, $data);

        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            $this->activityLogService->log(
                $updatedTask->project->user,
                'task_status_changed',
                $updatedTask,
                [
                    'title' => $updatedTask->title,
                    'from' => $oldStatus,
                    'to' => $updatedTask->status,
                ]
            );
        }

        return $updatedTask;
    });
}

    public function deleteTask(Task $task): bool
    {
        return DB::transaction(function () use ($task) {
            return $this->taskRepository->delete($task);
        });
    }
}