<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ActivityLogController extends Controller
{
    //
      public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

     public function index(Request $request): JsonResponse
    {
          $activities = $this->activityLogService->getUserActivities(
            $request->user(),
            $request->only(['per_page'])
        );
          return response()->json([
            'message' => 'Get activities successfully',
            'data' => ActivityLogResource::collection($activities),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }
}
