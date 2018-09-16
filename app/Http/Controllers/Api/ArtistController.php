<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ArtistRepositoryInterface;
use App\Http\Resources\ArtistResource;

class ArtistController extends ApiController
{

    /**
     * @var array $sharedRules
     */
    protected $sharedRules = [
        'moniker' => 'max:255',
        'alt_moniker' => 'max:255',
        'email' => 'email',
        'city' => 'max:255',
        'territory' => 'max:255',
        'country_code' => 'exists:countries,code',
        'official_url' => 'url',
        'profile_url' => 'max:64', // TODO This requires further validation, to prevent tomfoolery, profanity, etc..
    ];

    /**
     * @var array $storeRules
     */
    protected $storeRules = [
        'moniker' => 'required|max:255',
    ];

    /**
     * @var array $updateRules
     */
    protected $updateRules = [
        'moniker' => 'required|max:255', // TODO: should this really be required ? Per the tests it fails otherwise ...
    ];

    /**
     * Sets the RepositoryInterface to resolve
     *
     * @return string
     */
    public function repository()
    {
        return ArtistRepositoryInterface::class;
    }

    /**
     * Sets the ModelResource to resolve
     *
     * @return string
     */
    public function resource()
    {
        return ArtistResource::class;
    }
}
