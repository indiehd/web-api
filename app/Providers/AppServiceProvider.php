<?php

namespace App\Providers;

use App\Album;
use App\Artist;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\CountryRepositoryInterface;
use App\Contracts\DigitalAssetRepositoryInterface;
use App\Contracts\FeaturedRepositoryInterface;
use App\Contracts\FlacFileRepositoryInterface;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Genre;
use App\Label;
use App\Observers\AlbumObserver;
use App\Observers\ArtistObserver;
use App\Observers\GenreObserver;
use App\Observers\LabelObserver;
use App\Observers\UserObserver;
use App\Repositories\AccountRepository;
use App\Repositories\AlbumRepository;
use App\Repositories\ArtistRepository;
use App\Repositories\CatalogEntityRepository;
use App\Repositories\CountryRepository;
use App\Repositories\DigitalAssetRepository;
use App\Repositories\FeaturedRepository;
use App\Repositories\FlacFileRepository;
use App\Repositories\GenreRepository;
use App\Repositories\LabelRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\SongRepository;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Factory::guessFactoryNamesUsing(function ($name) {
            return (string) '\\Database\\Factories\\'.
                (class_basename($name)).
                'Factory';
        });

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(ArtistRepositoryInterface::class, ArtistRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(CatalogEntityRepositoryInterface::class, CatalogEntityRepository::class);
        $this->app->bind(AlbumRepositoryInterface::class, AlbumRepository::class);
        $this->app->bind(FlacFileRepositoryInterface::class, FlacFileRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(GenreRepositoryInterface::class, GenreRepository::class);
        $this->app->bind(LabelRepositoryInterface::class, LabelRepository::class);
        $this->app->bind(SongRepositoryInterface::class, SongRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(DigitalAssetRepositoryInterface::class, DigitalAssetRepository::class);
        $this->app->bind(FeaturedRepositoryInterface::class, FeaturedRepository::class);

        Artist::observe(ArtistObserver::class);
        Album::observe(AlbumObserver::class);
        Genre::observe(GenreObserver::class);
        Label::observe(LabelObserver::class);
        User::observe(UserObserver::class);
    }
}
