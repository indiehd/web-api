<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ArtistRepositoryInterface;
use App\Http\Requests\StoreArtist;
use App\Http\Resources\ArtistResource;
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
        return ArtistResource::collection(
            $this->artist->model()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreArtist $request
     * @return ArtistResource
     */
    public function store(StoreArtist $request)
    {
        return new ArtistResource($this->artist->create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return ArtistResource
     */
    public function show($id)
    {
        return new ArtistResource($this->artist->findById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreArtist $request
     * @param  int $id
     * @return ArtistResource
     */
    public function update(StoreArtist $request, $id)
    {
        $this->artist->update($id, $request->all());

        // Re-fetch the model so that it reflects the updates.

        return new ArtistResource($this->artist->findById($id));
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
