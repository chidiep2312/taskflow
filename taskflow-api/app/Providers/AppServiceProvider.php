<?php

namespace App\Providers;
use App\Repositories\DashboardRepository;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\TaskRepository;
use App\Repositories\Interfaces\ProjectMemberRepositoryInterface;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\TaskCommentRepository;
use App\Repositories\Interfaces\TaskAttachmentRepositoryInterface;
use App\Repositories\TaskAttachmentRepository;
use App\Repositories\Interfaces\TaskCommentRepositoryInterface;
use App\Repositories\Interfaces\ActivityLogRepositoryInterface;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
   public function register(): void
  {
    $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
    $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
    $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
    $this->app->bind(TaskCommentRepositoryInterface::class, TaskCommentRepository::class);
    $this->app->bind(TaskAttachmentRepositoryInterface::class, TaskAttachmentRepository::class);
    $this->app->bind(ProjectMemberRepositoryInterface::class, ProjectMemberRepository::class);
    $this->app->bind(
    DashboardRepositoryInterface::class,
    DashboardRepository::class
);
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
