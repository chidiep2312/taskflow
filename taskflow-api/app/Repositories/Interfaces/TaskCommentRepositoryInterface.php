<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Database\Eloquent\Collection;

interface TaskCommentRepositoryInterface
{
    public function getTaskComments(Task $task): Collection;

    public function create(array $data): TaskComment;

    public function delete(TaskComment $comment): bool;
}