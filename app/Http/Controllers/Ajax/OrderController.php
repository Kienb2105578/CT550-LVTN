<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\OrderServiceInterface  as OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface  as OrderRepository;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderService;
    protected $orderRepository;

    public function __construct(
        OrderService $orderService,
        OrderRepository $orderRepository,
    ) {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
    }


    public function update(Request $request)
    {
        if ($this->orderService->update($request)) {
            $order = $this->orderRepository->getOrderById($request->input('id'));

            if ($order && $order->confirm === 'cancle') {
                $this->orderService->updateCancel($order->id);
            }

            return response()->json([
                'error' => 10,
                'messages' => 'Cập nhật dữ liệu thành công',
                'order' => $order
            ]);
        }

        return response()->json([
            'error' => 11,
            'messages' => 'Cập nhật dữ liệu không thành công. Hãy thử lại'
        ]);
    }


    public function updateCancle(Request $request)
    {
        $order = $request->input('order');
        $id = $request->input('order.id');
        Log::info("UPDATECANCLE order ID: " . $id);

        $updateSuccess = $this->orderService->updateCancel($id);

        if ($updateSuccess) {
            Log::info("Update success for order ID: " . $id);
            $response = [
                'error' => 10,
                'messages' => 'Cập nhật dữ liệu thành công',
            ];
        } else {
            Log::error("Update failed for order ID: " . $id);
            $response = [
                'error' => 11,
                'messages' => 'Cập nhật dữ liệu không thành công. Hãy thử lại',
            ];
        }

        return response()->json($response);
    }
    public function updateReturn(Request $request)
    {
        $order = $request->input('order');
        $id = $request->input('order.id');
        $updateSuccess = $this->orderService->updateReturn($id);

        if ($updateSuccess) {
            Log::info("Update success for order ID: " . $id);
            $response = [
                'error' => 10,
                'messages' => 'Cập nhật dữ liệu thành công',
            ];
        } else {
            Log::error("Update failed for order ID: " . $id);
            $response = [
                'error' => 11,
                'messages' => 'Cập nhật dữ liệu không thành công. Hãy thử lại',
            ];
        }

        return response()->json($response);
    }



    public function chart(Request $request)
    {
        $chart = $this->orderService->ajaxOrderChart($request);

        return response()->json($chart);
    }

    public function getMyOrder(Request $request)
    {
        $confirm = $request->input('confirm', '');
        $payment = $request->input('payment', '');
        $delivery = $request->input('delivery', '');
        Log::info($confirm);
        Log::info($payment);
        Log::info($delivery);

        $orders = $this->orderRepository->getOrdersByStatus($confirm, $payment, $delivery);

        $orders = $orders->sortBy('created_at');
        foreach ($orders as &$order) {
            $order['products'] = $order['products']->values();
        }

        return response()->json($orders);
    }

    public function chartDoughnutChart(Request $request)
    {
        $chart = $this->orderRepository->getTopSellingProducts();
        Log::info("chart", ['Chart', $chart]);
        return response()->json($chart);
    }

    public function chartPolarChart(Request $request)
    {
        $chart = $this->orderRepository->chartPolar();
        Log::info("chart", ['Chart', $chart]);
        return response()->json($chart);
    }

    public function chartRevenueAndCost(Request $request)
    {
        $chart = $this->orderRepository->chartRevenueAndCost();
        return response()->json($chart);
    }
}
