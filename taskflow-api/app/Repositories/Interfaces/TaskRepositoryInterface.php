<?php

namespace App\Repositories\Interfaces;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function getProjectTasks(Project $project, array $filters = []): LengthAwarePaginator;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): bool;
}