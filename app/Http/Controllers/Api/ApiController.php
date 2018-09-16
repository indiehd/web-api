<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class ApiController extends Controller
{
    /**
     * @var $repository
     */
    protected $repository;

    /**
     * Should return the <RepositoryInterface>::class
     *
     * @return string
     */
    abstract public function repository();

    /**
     * @var $resource
     */
    protected $resource;

    /**
     * Should return the <Resource>::class
     *
     * @return string
     */
    abstract public function resource();

    /**
     * These rules are shared between both store and update
     *
     * @var array $sharedRules
     */
    protected $sharedRules = [];

    /**
     * Rules that are specific to the store request
     *
     * They will overwrite the rule set in $sharedRules
     *
     * @var array $storeRules
     */
    protected $storeRules = [];

    /**
     * Rules that are specific to the update request
     *
     * They will overwrite the rule set in $sharedRules
     *
     * @var array $updateRules
     */
    protected $updateRules = [];

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
     * @param $request
     * @return JsonResource
     */
    public function store(Request $request)
    {
        $request->validate(
            array_merge(
                $this->sharedRules,
                $this->storeRules
            )
        );

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
     * @param  $request
     * @param  int $id
     * @return JsonResource
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            array_merge(
                $this->sharedRules,
                $this->updateRules
            )
        );

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
