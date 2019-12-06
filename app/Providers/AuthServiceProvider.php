<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Policies\AlbumPolicy;
use App\Contracts\AlbumRepositoryInterface;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $albumRepository = resolve(AlbumRepositoryInterface::class);

        $this->policies[$albumRepository->class()] = AlbumPolicy::class;

        $this->registerPolicies();
    }
}
