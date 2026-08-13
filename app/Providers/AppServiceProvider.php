<?php

namespace App\Providers;

use App\Contracts\Repositories\CallRepositoryInterface;
use App\Contracts\Repositories\LeadRepositoryInterface;
use App\Repositories\EloquentCallRepository;
use App\Repositories\EloquentLeadRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LeadRepositoryInterface::class, EloquentLeadRepository::class);
        $this->app->bind(CallRepositoryInterface::class, EloquentCallRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
