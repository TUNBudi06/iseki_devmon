<?php

namespace App\Providers;

use App\Contracts\JwtServiceInterface;
use App\Contracts\PhotoStorageInterface;
use App\Services\JwtService;
use App\Services\LocalPhotoStorage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(JwtServiceInterface::class, JwtService::class);
        $this->app->bind(PhotoStorageInterface::class, LocalPhotoStorage::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
