<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getStatistics(User $user): array
    {
        $totalProject = Project::whereHas('members', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->count();
        $taskQuery = Task::whereHas('project.members', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        });
        $totalTask = (clone $taskQuery)->count();

        $statusStats = (clone $taskQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $priorityStats = (clone $taskQuery)
            ->selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        $overdueTasks = (clone $taskQuery)
            ->whereDate('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->count();

        return [
            'total_projects' => $totalProject,
            'total_tasks' => $totalTask,

            'status' => [
                'todo' => $statusStats['todo'] ?? 0,
                'in_progress' => $statusStats['in_progress'] ?? 0,
                'done' => $statusStats['done'] ?? 0,
            ],

            'priority' => [
                'high' => $priorityStats['high'] ?? 0,
                'medium' => $priorityStats['medium'] ?? 0,
                'low' => $priorityStats['low'] ?? 0,
            ],

            'overdue_tasks' => $overdueTasks,
        ];
    }
}
