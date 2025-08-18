<?php

namespace App\Providers;

use App\Repositories\ExamRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ExamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExamRepositoryInterface::class, ExamRepositoryInterface::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
