<?php

namespace App\Providers;

use App\Contracts;
use App\Policies;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
        $this->policies[resolve(Contracts\AlbumRepositoryInterface::class)->class()] = Policies\AlbumPolicy::class;
        $this->policies[resolve(Contracts\ArtistRepositoryInterface::class)->class()] = Policies\ArtistPolicy::class;
        $this->policies[resolve(Contracts\LabelRepositoryInterface::class)->class()] = Policies\LabelPolicy::class;
        $this->policies[resolve(Contracts\SongRepositoryInterface::class)->class()] = Policies\SongPolicy::class;
        $this->policies[resolve(Contracts\UserRepositoryInterface::class)->class()] = Policies\UserPolicy::class;

        $this->registerPolicies();
    }
}
