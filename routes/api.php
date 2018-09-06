<?php

use Illuminate\Http\Request;

use App\Artist;
use App\Http\Resources\CatalogResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Api')->group(function () {

    /*
     * Users
     */
    Route::prefix('users')->group(function () {
        Route::post('/{id}/password', 'PasswordController@update')->name('password.update');
    });

    /*
     * Users
     */
    Route::prefix('users')->group(function () {
        Route::get('/', 'UserController@all')->name('user.index');
        Route::get('/{id}', 'UserController@show')->name('user.show');
        Route::post('/create', 'UserController@store')->name('user.store');
        Route::put('/{id}', 'UserController@update')->name('user.update');
        Route::delete('/{id}', 'UserController@destroy')->name('user.destroy');
    });

    /*
     * Artists
     */
    Route::prefix('artists')->group(function () {
        Route::get('/', 'ArtistController@index')->name('artist.index');
        Route::get('/{id}', 'ArtistController@show')->name('artist.show');
        Route::post('/create', 'ArtistController@store')->name('artist.store');
        Route::put('/{id}', 'ArtistController@update')->name('artist.update');
        Route::delete('/{id}', 'ArtistController@destroy')->name('artist.destroy');
    });
});
