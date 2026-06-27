<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\DashboardRepositoryInterface;

class DashboardService
{
    public function __construct(
        protected DashboardRepositoryInterface $dashboardRepository
    ) {}

    public function getStatistics(User $user): array
    {
        return $this->dashboardRepository->getStatistics($user);
    }
}