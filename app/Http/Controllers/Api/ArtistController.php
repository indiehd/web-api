<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ArtistRepositoryInterface;
use App\Http\Requests\StoreArtist;
use App\Http\Resources\CatalogResource;
use App\Http\Controllers\Controller;

class ArtistController extends Controller
{

    /**
     * @var ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * ArtistController constructor.
     *
     * @param ArtistRepositoryInterface $artist
     */
    public function __construct(ArtistRepositoryInterface $artist)
    {
        $this->artist = $artist;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CatalogResource::collection(
            $this->artist->model()
                ->with('profile')
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreArtist $request
     * @return CatalogResource
     */
    public function store(StoreArtist $request)
    {
        return new CatalogResource($this->artist->create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return CatalogResource
     */
    public function show($id)
    {
        return new CatalogResource($this->artist->findById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreArtist $request
     * @param  int $id
     * @return CatalogResource
     */
    public function update(StoreArtist $request, $id)
    {
        $this->artist->update($id, $request->all());

        // Re-fetch the model so that it reflects the updates.

        return new CatalogResource($this->artist->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $artist = $this->artist->findById($id);
        $artist->delete();

        return response(['success' => true]);
    }
}
