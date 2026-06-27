<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
class ActivityLogService
{
    public function __construct(
    protected ActivityLogRepositoryInterface $activityLogRepository
) {}

    public function log(?User $user, string $action, ?object $model = null, array $data = []): void
    {
        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model' => $model ? get_class($model) : null,
            'model_id' => $model?->id ?? null,
            'data' => $data,
        ]);
    }

    public function getUserActivities(User $user, array $filters = []): LengthAwarePaginator
{
    return $this->activityLogRepository->getUserActivities($user, $filters);
}
}