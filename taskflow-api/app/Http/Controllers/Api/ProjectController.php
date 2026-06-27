<?php

namespace App\Http\Controllers\Api;
use App\Traits\ApiResponse;
use App\Services\ProjectService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
class ProjectController extends Controller
{
      use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function __construct(
        protected ProjectService $projectService
    ) {}
    public function index(Request $request): JsonResponse
    {
        $projects = $this->projectService->getUserProjects(
            $request->user(),
            $request->only(['search', 'status', 'per_page'])
        );

        return response()->json([
            'message' => 'Get projects successfully',
            'data' => ProjectResource::collection($projects),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject(
            $request->user(),
            $request->validated()
        );

       return $this->successResponse(
          new ProjectResource($project),
          'Create project successfully',
           201
         );
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json([
            'message' => 'Get project successfully',
            'data' => new ProjectResource($project->loadCount('tasks')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $project = $this->projectService->updateProject(
            $project,
            $request->validated()
        );

        return response()->json([
            'message' => 'Update project successfully',
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $this->projectService->deleteProject($project);

        return response()->json([
            'message' => 'Delete project successfully',
        ]);
    }
}
