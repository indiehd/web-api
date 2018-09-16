<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AlbumRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlbumResource;
use Illuminate\Http\Request;

class AlbumController extends Controller
{

    /**
     * @var AlbumRepositoryInterface
     */
    private $album;

    /**
     * AlbumController constructor.
     *
     * @param AlbumRepositoryInterface $album
     */
    public function __construct(AlbumRepositoryInterface $album)
    {
        $this->album = $album;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function all()
    {
        return AlbumResource::collection(
            $this->album->all()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return AlbumResource
     */
    public function store(Request $request)
    {
        // TODO: Create StoreAlbum request
        return new AlbumResource($this->album->create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return AlbumResource
     */
    public function show($id)
    {
        return new AlbumResource($this->album->findById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return AlbumResource
     */
    public function update(Request $request, $id)
    {
        // TODO: Create UpdateAlbum request
        $this->album->update($id, $request->all());

        return new AlbumResource($this->album->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->album->delete($id);

        return response(['success' => true], 200);
    }
}
