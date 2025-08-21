<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TeacherCredentialRepository;
use App\Interfaces\TeacherCredentialRepositoryInterface;
class TeacherCredServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
        TeacherCredentialRepositoryInterface::class,
        TeacherCredentialRepository::class
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
