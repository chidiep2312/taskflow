<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Repositories\Interfaces\TaskCommentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskCommentService
{
    public function __construct(
        protected TaskCommentRepositoryInterface $taskCommentRepository,
        protected ActivityLogService $activityLogService
    ) {}

    public function getTaskComments(Task $task): Collection
    {
        return $this->taskCommentRepository->getTaskComments($task);
    }

    public function createComment(User $user, Task $task, array $data): TaskComment
    {
        return DB::transaction(function () use ($user, $task, $data) {
            $comment = $this->taskCommentRepository->create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'content' => $data['content'],
            ]);

            $this->activityLogService->log(
                $user,
                'task_commented',
                $task,
                [
                    'task_title' => $task->title,
                    'content' => $comment->content,
                ]
            );

            return $comment;
        });
    }

    public function deleteComment(TaskComment $comment): bool
    {
        return DB::transaction(function () use ($comment) {
            return $this->taskCommentRepository->delete($comment);
        });
    }
}