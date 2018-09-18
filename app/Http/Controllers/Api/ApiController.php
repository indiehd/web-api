<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class ApiController extends Controller
{

    /**
     * @var string $repository
     */
    private $repository;

    /**
     * Should return the <RepositoryInterface>::class
     *
     * @return string
     */
    abstract public function repository();

    /**
     * @var string $resource
     */
    private $resource;

    /**
     * Should return the <Resource>::class
     *
     * @return string
     */
    abstract public function resource();

    /**
     * Should return <StoreRequest>::class
     *
     * @return string
     */
    abstract public function storeRequest();

    /**
     * Should return <UpdateRequest>::class
     *
     * @return string
     */
    abstract public function updateRequest();

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->repository = resolve($this->repository());
        $this->resource = $this->resource();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function all()
    {
        return $this->resource::collection(
            $this->repository->all()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResource
     */
    public function store(Request $request)
    {
        resolve($this->storeRequest());

        return new $this->resource($this->repository->create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return JsonResource
     */
    public function show($id)
    {
        return new $this->resource($this->repository->findById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return JsonResource
     */
    public function update(Request $request, $id)
    {
        resolve($this->updateRequest());

        $this->repository->update($id, $request->all());

        return new $this->resource($this->repository->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return response(['success' => true], 200);
    }
}
