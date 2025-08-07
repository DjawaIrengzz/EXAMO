<?php

namespace App\Providers;

use App\Repositories\Interfaces\ExamResultRepositoryInterface;
use App\Services\ExamResultService;
use App\Services\Interfaces\ExamResultServiceInterface;
use ExamResultRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExamResultServiceInterface::class, ExamResultService::class);
    $this->app->bind(ExamResultRepositoryInterface::class, ExamResultRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
