<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Repositories\Interfaces\TaskAttachmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskAttachmentRepository implements TaskAttachmentRepositoryInterface
{
    public function getTaskAttachments(Task $task): Collection
    {
        return TaskAttachment::query()
            ->where('task_id', $task->id)
            ->with('user:id,name')
            ->latest()
            ->get();
    }

    public function create(array $data): TaskAttachment
    {
        return TaskAttachment::create($data)->load('user:id,name');
    }

    public function delete(TaskAttachment $attachment): bool
    {
        return $attachment->delete();
    }
}