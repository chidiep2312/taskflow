<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TaskComment;
use App\Repositories\Interfaces\TaskCommentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskCommentRepository implements TaskCommentRepositoryInterface
{
    public function getTaskComments(Task $task): Collection
    {
        return TaskComment::query()
            ->where('task_id', $task->id)
            ->with('user:id,name')
            ->latest()
            ->get();
    }

    public function create(array $data): TaskComment
    {
        return TaskComment::create($data)->load('user:id,name');
    }

    public function delete(TaskComment $comment): bool
    {
        return $comment->delete();
    }
}