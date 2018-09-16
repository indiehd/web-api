<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FlacFileResource;
use App\Repositories\FlacFileRepository;
use Illuminate\Http\Request;

class FlacFileController extends Controller
{

    /**
     * @var FlacFileRepository
     */
    private $flacFile;

    /**
     * FlacFileController constructor.
     *
     * @param FlacFileRepository $flacFile
     */
    public function __construct(FlacFileRepository $flacFile)
    {
        $this->flacFile = $flacFile;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function all()
    {
        return FlacFileResource::collection(
            $this->flacFile->all()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return FlacFileResource
     */
    public function store(Request $request)
    {
        // TODO: Create StoreFlacFile request
        return new FlacFileResource($this->flacFile->create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return FlacFileResource
     */
    public function show($id)
    {
        return new FlacFileResource($this->flacFile->findById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return FlacFileResource
     */
    public function update(Request $request, $id)
    {
        // TODO: Create UpdateFlacFile request
        $this->flacFile->update($id, $request->all());

        return new FlacFileResource($this->flacFile->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->flacFile->delete($id);

        return response(['success' => true], 200);
    }
}
