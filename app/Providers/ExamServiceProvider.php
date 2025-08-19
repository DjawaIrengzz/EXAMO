<?php

namespace App\Providers;

use App\Repositories\ExamRepository;
use App\Repositories\Interfaces\ExamRepositoryInterface;
use App\Services\ExamService;
use App\Services\Interfaces\ExamServiceInterface;
use Illuminate\Support\ServiceProvider;

class ExamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
