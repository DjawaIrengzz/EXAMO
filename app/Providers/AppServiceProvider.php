<?php

namespace App\Providers;

use App\Repositories\ExamRepository;
use App\Repositories\Interfaces\ExamRepositoryInterface;
use App\Services\ExamService;
use App\Services\Interfaces\ExamServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ExamRepositoryInterface::class,
            ExamRepository::class
        );

        // Service binding
        $this->app->bind(
            ExamServiceInterface::class,
            ExamService::class
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
