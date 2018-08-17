<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ArtistRepositoryInterface;
use App\Http\Resources\CatalogResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArtistController extends Controller
{

    /**
     * @var ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * ArtistController constructor.
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
     * @param  \Illuminate\Http\Request $request
     * @return CatalogResource
     */
    public function store(Request $request)
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
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return CatalogResource
     */
    public function update(Request $request, $id)
    {
        $artist = $this->artist->findById($id);
        $artist->update($request->all());

        return new CatalogResource($artist);
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
