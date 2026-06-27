<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function getUserActivities(User $user, array $filters = []): LengthAwarePaginator
    {
        return ActivityLog::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($filters['per_page'] ?? 10);
    }
}