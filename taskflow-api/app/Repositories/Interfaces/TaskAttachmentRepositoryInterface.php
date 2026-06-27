<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Database\Eloquent\Collection;

interface TaskAttachmentRepositoryInterface
{
    public function getTaskAttachments(Task $task): Collection;

    public function create(array $data): TaskAttachment;

    public function delete(TaskAttachment $attachment): bool;
}