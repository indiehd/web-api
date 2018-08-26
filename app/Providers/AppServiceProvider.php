<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\FlacFileRepositoryInterface;
use App\Contracts\CountryRepositoryInterface;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\SkuRepositoryInterface;

use App\Repositories\ArtistRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\UserRepository;
use App\Repositories\AlbumRepository;
use App\Repositories\FlacFileRepository;
use App\Repositories\CountryRepository;
use App\Repositories\GenreRepository;
use App\Repositories\LabelRepository;
use App\Repositories\SkuRepository;

use App\Artist;
use App\Album;
use App\Genre;
use App\Label;
use App\Observers\ArtistObserver;
use App\Observers\AlbumObserver;
use App\Observers\GenreObserver;
use App\Observers\LabelObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Artist::observe(ArtistObserver::class);
        Album::observe(AlbumObserver::class);
        Genre::observe(GenreObserver::class);
        Label::observe(LabelObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ArtistRepositoryInterface::class, ArtistRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(AlbumRepositoryInterface::class, AlbumRepository::class);
        $this->app->bind(FlacFileRepositoryInterface::class, FlacFileRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(GenreRepositoryInterface::class, GenreRepository::class);
        $this->app->bind(LabelRepositoryInterface::class, LabelRepository::class);
        $this->app->bind(SkuRepositoryInterface::class, SkuRepository::class);
    }
}
