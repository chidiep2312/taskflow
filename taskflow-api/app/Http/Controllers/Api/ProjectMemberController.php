<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectMember\StoreProjectMemberRequest;
use App\Http\Requests\ProjectMember\UpdateProjectMemberRoleRequest;
use App\Http\Resources\ProjectMemberResource;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectMemberService;
use Illuminate\Http\JsonResponse;

class ProjectMemberController extends Controller
{
    public function __construct(
        protected ProjectMemberService $projectMemberService
    ) {}

    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $members = $this->projectMemberService->getMembers($project);

        return response()->json([
            'message' => 'Get project members successfully',
            'data' => ProjectMemberResource::collection($members),
        ]);
    }

    public function store(StoreProjectMemberRequest $request, Project $project): JsonResponse
    {
        $this->authorize('manageMembers', $project);

        $this->projectMemberService->addMember(
            $request->user(),
            $project,
            $request->validated()
        );

        return response()->json([
            'message' => 'Add member successfully',
        ], 201);
    }

    public function updateRole(
        UpdateProjectMemberRoleRequest $request,
        Project $project,
        User $member
    ): JsonResponse {
        $this->authorize('manageMembers', $project);

        $this->projectMemberService->updateRole(
            $request->user(),
            $project,
            $member,
            $request->validated()['role']
        );

        return response()->json([
            'message' => 'Update member role successfully',
        ]);
    }

    public function destroy(Project $project, User $member): JsonResponse
    {
        $this->authorize('manageMembers', $project);

        $this->projectMemberService->removeMember(
            request()->user(),
            $project,
            $member
        );

        return response()->json([
            'message' => 'Remove member successfully',
        ]);
    }
}