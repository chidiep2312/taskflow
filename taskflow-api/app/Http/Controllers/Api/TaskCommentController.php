<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskComment\StoreTaskCommentRequest;
use App\Http\Resources\TaskCommentResource;
use App\Models\Task;
use App\Models\TaskComment;
use App\Services\TaskCommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function __construct(
        protected TaskCommentService $taskCommentService
    ) {}

    public function index(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $comments = $this->taskCommentService->getTaskComments($task);

        return response()->json([
            'message' => 'Get comments successfully',
            'data' => TaskCommentResource::collection($comments),
        ]);
    }

    public function store(StoreTaskCommentRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $comment = $this->taskCommentService->createComment(
            $request->user(),
            $task,
            $request->validated()
        );

        return response()->json([
            'message' => 'Create comment successfully',
            'data' => new TaskCommentResource($comment),
        ], 201);
    }

    public function destroy(TaskComment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->taskCommentService->deleteComment($comment);

        return response()->json([
            'message' => 'Delete comment successfully',
        ]);
    }
}
