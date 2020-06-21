<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class ApiController extends Controller
{
    /**
     * When true authorization is enabled
     *
     * @var bool
     */
    protected $shouldAuthorize = false;

    /**
     * @var \App\Contracts\RepositoryShouldCrud|\App\Contracts\RepositoryShouldRead $repository
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
     * Should return <DestroyRequest>::class
     *
     * @return string
     */
    abstract public function destroyRequest();

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
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'paginate' => 'numeric|min:1|max:100',
            'limit' => 'numeric|min:1'
        ]);

        $this->authorize('viewAny', $this->repository->class());

        $hasPaginate = $request->has('paginate');
        $hasLimit = $request->has('limit');

        if ($hasPaginate && !$hasLimit) {
            $models = $this->repository
                ->paginate($request->get('paginate'));
        } elseif ($hasLimit && !$hasPaginate) {
            $models = $this->repository
                ->limit($request->get('limit'));
        } elseif ($hasPaginate && $hasLimit) {
            $models = $this->repository
                ->limit(
                    $request->get('limit'),
                    $request->get('paginate')
                );
        }


        return $this->resource::collection(
            isset($models) ? $models : $this->repository->all()
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

        if ($this->shouldAuthorize) {
            $this->authorize('create', $this->repository->class());
        }

        return new $this->resource($this->repository->create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResource
     */
    public function show($id)
    {
        $model = $this->repository->findById($id);

        if ($this->shouldAuthorize) {
            $this->authorize('view', $model);
        }

        return new $this->resource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResource
     */
    public function update(Request $request, $id)
    {
        resolve($this->updateRequest());

        if ($this->shouldAuthorize) {
            $this->authorize('update', $this->repository->findById($id));
        }

        $this->repository->update($id, $request->all());

        return new $this->resource($this->repository->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        resolve($this->destroyRequest());

        if ($this->shouldAuthorize) {
            $this->authorize('delete', $this->repository->findById($id));
        }

        $this->repository->delete($id);

        return response(['success' => true], 200);
    }
}
