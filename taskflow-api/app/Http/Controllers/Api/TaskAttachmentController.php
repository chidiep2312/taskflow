<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskAttachment\StoreTaskAttachmentRequest;
use App\Http\Resources\TaskAttachmentResource;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Services\TaskAttachmentService;
use Illuminate\Http\JsonResponse;

class TaskAttachmentController extends Controller
{
    public function __construct(
        protected TaskAttachmentService $taskAttachmentService
    ) {}

    public function index(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $attachments = $this->taskAttachmentService->getTaskAttachments($task);

        return response()->json([
            'message' => 'Get attachments successfully',
            'data' => TaskAttachmentResource::collection($attachments),
        ]);
    }

    public function store(StoreTaskAttachmentRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $attachment = $this->taskAttachmentService->uploadAttachment(
            $request->user(),
            $task,
            $request->file('file')
        );

        return response()->json([
            'message' => 'Upload attachment successfully',
            'data' => new TaskAttachmentResource($attachment),
        ], 201);
    }

    public function destroy(TaskAttachment $attachment): JsonResponse
    {
        $this->authorize('delete', $attachment);

        $this->taskAttachmentService->deleteAttachment($attachment);

        return response()->json([
            'message' => 'Delete attachment successfully',
        ]);
    }
}