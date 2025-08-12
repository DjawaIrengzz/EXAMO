<?php

namespace App\Providers;

use App\Exports\Contracts\ExamResultExporterInterface;
use App\Exports\ExamResultExporter;
use App\Repositories\Interfaces\ExamResultRepositoryInterface;
use App\Services\ExamResultService;
use App\Services\Interfaces\ExamResultServiceInterface;
use ExamResultRepository;
use Illuminate\Support\ServiceProvider;

class UserResultServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExamResultServiceInterface::class, ExamResultService::class);
    $this->app->bind(ExamResultRepositoryInterface::class, ExamResultRepository::class);
        $this->app->bind(ExamResultExporterInterface::class, ExamResultExporter::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
