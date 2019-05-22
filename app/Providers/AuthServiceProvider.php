<?php

namespace App\Providers;

use App\Contracts;
use App\Policies;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

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
        $this->policies[resolve(Contracts\FeaturedRepositoryInterface::class)->class()]
            = Policies\FeaturedPolicy::class;
        $this->policies[resolve(Contracts\GenreRepositoryInterface::class)->class()] = Policies\GenrePolicy::class;
        $this->policies[resolve(Contracts\LabelRepositoryInterface::class)->class()] = Policies\LabelPolicy::class;
        $this->policies[resolve(Contracts\OrderRepositoryInterface::class)->class()] = Policies\OrderPolicy::class;
        $this->policies[resolve(Contracts\SongRepositoryInterface::class)->class()] = Policies\SongPolicy::class;
        $this->policies[resolve(Contracts\UserRepositoryInterface::class)->class()] = Policies\UserPolicy::class;

        $this->registerPolicies();

        Passport::routes();
    }
}
