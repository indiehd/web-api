<?php

namespace App\Http\Controllers\Api;

use App\Contracts\OrderItemRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Http\Requests\DestroyOrder;
use App\Http\Requests\StoreOrder;
use App\Http\Requests\UpdateOrder;
use App\Http\Resources\OrderResource;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends ApiController
{
    protected $shouldAuthorize = true;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct();

        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Should return the <RepositoryInterface>::class.
     *
     * @return string
     */
    public function repository()
    {
        return OrderRepositoryInterface::class;
    }

    /**
     * Should return the <Resource>::class.
     *
     * @return string
     */
    public function resource()
    {
        return OrderResource::class;
    }

    /**
     * Should return <StoreRequest>::class.
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreOrder::class;

        // TODO Due to the parent class, the Request cannot be type-hinted on
        // this method. How best to access the Request object, then?
    }

    /**
     * Should return <UpdateRequest>::class.
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateOrder::class;
    }

    /**
     * Should return <DestroyRequest>::class.
     *
     * @return string
     */
    public function destroyRequest()
    {
        return DestroyOrder::class;
    }

    /**
     * Create an Order, which requires adding one or more Items to the Order.
     *
     * @param Request $request
     * @return Response
     */
    public function storeOrder(StoreOrder $request)
    {
        $this->authorize('create', $this->orderRepository->class());

        if (isset($request->input('items')[0]) && is_array($request->input('items')[0])) {
            $items = $request->input('items');
        } else {
            $items = [$request->input('items')];
        }

        $order = $this->orderRepository->model()->create();

        DB::transaction(function () use ($items, $order) {
            foreach ($items as $item) {
                $item['order_id'] = $order->id;

                $this->orderItemRepository->create($item);
            }
        });

        return response()->json(
            ['data' => $this->orderRepository->findById($order->id)],
            201
        );
    }

    /**
     * Add one or more Items to an existing Order.
     *
     * @param UpdateOrder $request
     * @return Response
     */
    public function addItems(UpdateOrder $request)
    {
        $order = $this->orderRepository->findById($request->route('orderId'));

        $this->authorize('update', $order);

        if (isset($request->input('items')[0]) && is_array($request->input('items')[0])) {
            $items = $request->input('items');
        } else {
            $items = [$request->input('items')];
        }

        DB::transaction(function () use ($items, $order) {
            foreach ($items as $item) {
                $item['order_id'] = $order->id;

                $this->orderItemRepository->create($item);
            }
        });

        return response()->json(
            ['data' => $this->orderRepository->findById($order->id)],
            201
        );
    }

    /**
     * Remove one or more Items from an existing Order.
     *
     * @param UpdateOrder $request
     * @return Response
     */
    public function removeItems(UpdateOrder $request)
    {
        $order = $this->orderRepository->findById($request->route('orderId'));

        $this->authorize('update', $order);

        if (isset($request->input('items')[0]) && is_array($request->input('items')[0])) {
            $items = $request->input('items');
        } else {
            $items = [$request->input('items')];
        }

        DB::transaction(function () use ($items, $order) {
            foreach ($items as $item) {
                $item['order_id'] = $order->id;

                $model = $this->orderItemRepository->findByOrderId(
                    $item['order_id'],
                    $item['orderable_id'],
                    $item['orderable_type']
                );

                $this->orderItemRepository->delete($model->id);
            }
        });

        return response(['success' => true], 200);
    }
}
