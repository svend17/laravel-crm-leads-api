<?php

namespace App\Providers;

use App\Actions\CreateCallAction;
use App\Contracts\Actions\CreatesCalls;
use App\Contracts\Repositories\CallRepositoryInterface;
use App\Contracts\Repositories\LeadRepositoryInterface;
use App\Repositories\EloquentCallRepository;
use App\Repositories\EloquentLeadRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings.
     */
    public function register(): void
    {
        $this->app->bind(CreatesCalls::class, CreateCallAction::class);
        $this->app->bind(LeadRepositoryInterface::class, EloquentLeadRepository::class);
        $this->app->bind(CallRepositoryInterface::class, EloquentCallRepository::class);
    }
}
