<?php

namespace App\Repositories\Interfaces;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DashboardRepositoryInterface
{
 public function getStatistics(User $user): array;   
}