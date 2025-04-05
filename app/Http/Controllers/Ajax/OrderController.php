<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\OrderServiceInterface  as OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface  as OrderRepository;
use Illuminate\Support\Facades\Log;

use App\Models\ProductVariant;
use App\Models\Product;

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

        $updateSuccess = $this->orderService->updateCancel($id);

        if ($updateSuccess) {
            $response = [
                'error' => 10,
                'messages' => 'Cập nhật dữ liệu thành công',
            ];
        } else {
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
            $response = [
                'error' => 10,
                'messages' => 'Cập nhật dữ liệu thành công',
            ];
        } else {
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

    public function getVariantByProduct(Request $request)
    {
        $productId = $request->input('product_id');
        $variants = ProductVariant::where('product_id', $productId)
            ->select('id', 'name', 'price')
            ->get();
        return response()->json($variants);
    }

    public function getProduct(Request $request)
    {
        $productId = $request->input('product_id');
        $variantId = $request->input('variant_id');
        $product = Product::find($productId);
        if ($variantId && $variantId !== 'null') {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $product->product_name = $product->name . " - " . $variant->name;
                $product->variant_id = $variant->id;
                $product->variant_price = $variant->price;
            }
        } else {
            $product->product_name = $product->name;
            $product->variant_id = null;
            $product->variant_price = $product->price;
        }
        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'price' => $product->variant_price,
            'variant_id' => $product->variant_id
        ]);
    }


    public function getMyOrder(Request $request)
    {
        $confirm = $request->input('confirm', '');
        $payment = $request->input('payment', '');
        $delivery = $request->input('delivery', '');

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
