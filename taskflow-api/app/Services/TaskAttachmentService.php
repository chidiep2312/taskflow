<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use App\Repositories\Interfaces\TaskAttachmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentService
{
    public function __construct(
        protected TaskAttachmentRepositoryInterface $taskAttachmentRepository,
        protected ActivityLogService $activityLogService
    ) {}

    public function getTaskAttachments(Task $task): Collection
    {
        return $this->taskAttachmentRepository->getTaskAttachments($task);
    }

    public function uploadAttachment(User $user, Task $task, UploadedFile $file): TaskAttachment
    {
        return DB::transaction(function () use ($user, $task, $file) {
            $path = $file->store('task-attachments', 'public');

            $attachment = $this->taskAttachmentRepository->create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $this->activityLogService->log(
                $user,
                'task_attachment_uploaded',
                $task,
                [
                    'task_title' => $task->title,
                    'file_name' => $attachment->file_name,
                ]
            );

            return $attachment;
        });
    }

    public function deleteAttachment(TaskAttachment $attachment): bool
    {
        return DB::transaction(function () use ($attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            return $this->taskAttachmentRepository->delete($attachment);
        });
    }
}