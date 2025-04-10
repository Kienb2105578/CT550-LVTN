<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface  as PurchaseOrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

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

        $totalQuantity = DB::table('inventory_batches')
            ->where('product_id', $product->id)
            ->whereNull('variant_id')
            ->where('publish', 2)
            ->sum('quantity');

        $variantsQuantities = $product->product_variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'quantity' => DB::table('inventory_batches')
                    ->where('variant_id', $variant->id)
                    ->where('publish', 2)
                    ->sum('quantity'),
                'code' => $variant->sku,
                'name' => $variant->name,
            ];
        });

        Log::info("Product", ["product" => $product]);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => $totalQuantity,
            'status' => $product->status,
            'price' => 0,
            'variants' => $variantsQuantities
        ]);
    }


    public function loadExistingProducts($orderId)
    {
        $order = PurchaseOrder::find($orderId);

        if (!$order) {
            return response()->json(['error' => 'Đơn hàng không tồn tại'], 404);
        }

        $products = [];

        foreach ($order->purchase_order_details as $detail) {
            $productId = $detail->product_id;
            $variantId = $detail->variant_id;

            // Lấy tên sản phẩm
            $productName = Product::where('id', $productId)->pluck('name')->first();

            // Kiểm tra nếu sản phẩm đã tồn tại trong danh sách
            if (!isset($products[$productId])) {
                $products[$productId] = [
                    'id' => $productId,
                    'name' => $productName,
                    'quantity' => 0, // Số lượng tổng cộng
                    'price' => 0,    // Tổng giá
                    'status' => $order->status,
                    'variants' => [], // Mảng biến thể
                ];
            }

            // Biến lưu trữ số lượng và giá trị cho biến thể
            $totalQuantity = 0;
            $productPrice = 0;

            // Kiểm tra nếu có biến thể
            if ($variantId != null) {
                $variant = ProductVariant::find($variantId);

                // Nếu có biến thể, thêm thông tin vào mảng 'variants'
                if ($variant) {
                    $variantQuantity = $detail->quantity;
                    $variantPrice = $detail->price;

                    // Cộng dồn số lượng và giá sản phẩm
                    $totalQuantity += $variantQuantity;
                    $productPrice += $variantPrice;

                    $products[$productId]['variants'][] = [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'quantity' => $variantQuantity,
                        'price' => (int) $variantPrice,
                    ];
                }
            } else {
                // Nếu không có biến thể, dùng thông tin của sản phẩm chính
                $totalQuantity = $detail->quantity;
                $productPrice = $detail->price;
            }

            // Cập nhật số lượng và giá sản phẩm chính
            $products[$productId]['quantity'] += $totalQuantity;
            $products[$productId]['price'] += $productPrice;
        }

        return response()->json([
            'status' => $order->status,
            'details' => array_values($products)
        ]);
    }
}
