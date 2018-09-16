<?php

namespace App\Http\Controllers\Api;

use App\Contracts\SongRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\SongResource;
use Illuminate\Http\Request;

class SongController extends Controller
{

    /**
     * @var SongRepositoryInterface
     */
    private $song;

    /**
     * SongController constructor.
     *
     * @param SongRepositoryInterface $song
     */
    public function __construct(SongRepositoryInterface $song)
    {
        $this->song = $song;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function all()
    {
        return SongResource::collection(
            $this->song->all()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return SongResource
     */
    public function store(Request $request)
    {
        // TODO: Create StoreSong request
        return new SongResource($this->song->create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return SongResource
     */
    public function show($id)
    {
        return new SongResource($this->song->findById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return SongResource
     */
    public function update(Request $request, $id)
    {
        // TODO: Create UpdateSong request
        $this->song->update($id, $request->all());

        return new SongResource($this->song->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->song->delete($id);

        return response(['success' => true], 200);
    }
}
