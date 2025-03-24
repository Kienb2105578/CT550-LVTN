<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface  as PurchaseOrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\PurchaseOrder;

class PurchaseOrderController extends Controller
{

    protected $purchaseOrderRepository;

    public function __construct(
        PurchaseOrderRepository $purchaseOrderRepository,
    ) {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        parent::__construct();
    }

    public function getProductDetails(Request $request)
    {
        Log::info($request->product_id);
        $product = Product::with('product_variants')->find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }
        Log::info("Product", ["product", $product]);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => $product->quantity,
            'status' => $product->status,
            'price' => 0,
            'variants' => $product->product_variants?->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'code' => $variant->sku,
                    'name' => $variant->name,
                    'quantity' => $variant->quantity
                ];
            }) ?? collect([])
        ]);
    }
    public function loadExistingProducts($orderId)
    {
        $order = PurchaseOrder::with(['purchase_order_details.product.product_variants'])
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Đơn hàng không tồn tại'], 404);
        }

        $products = [];

        foreach ($order->purchase_order_details as $detail) {
            $productId = $detail->product->id;
            if (!isset($products[$productId])) {
                $totalQuantity = 0;
                $productPrice = 0;

                if ($detail->product->product_variants->isNotEmpty()) {
                    $variantQuantities = $detail->product->product_variants->sum(function ($variant) use ($detail) {
                        return $detail->quantity;
                    });

                    $totalQuantity += $variantQuantities;
                    $productPrice = $detail->product->product_variants->sum(function ($variant) use ($detail) {
                        return $detail->price * $detail->quantity;
                    });

                    $products[$productId] = [
                        'id' => $detail->product->id,
                        'name' => $detail->product->name,
                        'quantity' => $totalQuantity,
                        'price' => (int)$productPrice,
                        'status' => $order->status,
                        'variants' => $detail->product->product_variants->map(function ($variant) use ($detail) {
                            return [
                                'id' => $variant->id,
                                'name' => $variant->name,
                                'quantity' => $detail->quantity,
                                'price' => (int) $detail->price
                            ];
                        })
                    ];
                } else {
                    $totalQuantity = $detail->quantity;
                    $productPrice = $detail->price * $totalQuantity;

                    $products[$productId] = [
                        'id' => $detail->product->id,
                        'name' => $detail->product->name,
                        'quantity' => $totalQuantity,
                        'price' => (int)$detail->price,
                        'status' => $order->status,
                        'variants' => []
                    ];
                }
            }
        }
        $productDetails = array_values($products);

        // Thêm status của phiếu nhập hàng vào response
        return response()->json([
            'status' => $order->status, // Lấy status của purchase_order
            'details' => $productDetails
        ]);
    }
}
