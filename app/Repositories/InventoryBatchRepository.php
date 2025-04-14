<?php

namespace App\Repositories;

use App\Models\InventoryBatch;
use App\Repositories\Interfaces\InventoryBatchRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ProductVariant;

/**
 * Class UserService
 * @package App\Services
 */
class InventoryBatchRepository extends BaseRepository implements InventoryBatchRepositoryInterface
{
    protected $model;

    public function __construct(
        InventoryBatch $model
    ) {
        $this->model = $model;
    }

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 1,
        array $extend = [],
        array $orderBy = ['created_at', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = []
    ) {
        $query = $this->model->select($column);

        return $query
            ->when(!empty($condition['keyword']), function ($q) use ($condition) {
                $q->where(function ($subQuery) use ($condition) {
                    $subQuery->whereHas('products', function ($productQuery) use ($condition) {
                        $productQuery->where('name', 'LIKE', '%' . $condition['keyword'] . '%');
                    })
                        ->orWhereHas('purchaseOrder.supplier', function ($supplierQuery) use ($condition) {
                            $supplierQuery->where('name', 'LIKE', '%' . $condition['keyword'] . '%');
                        });
                });
            })
            ->publish($condition['publish'] ?? null)
            ->customDropdownFilter($condition['dropdown'] ?? null)
            ->relationCount($relations ?? null)
            ->CustomWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->customJoin([
                ['products', 'products.id', '=', 'inventory_batches.product_id'],
                ['purchase_orders', 'purchase_orders.id', '=', 'inventory_batches.purchase_order_id'],
                ['suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id'],
            ])
            ->groupBy('purchase_order_id')
            ->customGroupBy($extend['groupBy'] ?? null)
            ->customerCreatedAt($condition['created_at'] ?? null)
            ->orderBy($orderBy[0] ?? 'created_at', $orderBy[1] ?? 'DESC')
            ->paginate($perPage)
            ->withQueryString()->withPath(env('APP_URL') . ($extend['path'] ?? ''));
    }
    public function getInventoryWithPurchase($purchaseOrderId)
    {
        return DB::table('inventory_batches AS ib')
            ->join('products AS p', 'p.id', '=', 'ib.product_id')
            ->leftJoin('product_variants AS pv', 'pv.id', '=', 'ib.variant_id')
            ->where('ib.purchase_order_id', $purchaseOrderId)
            ->select([
                'ib.id',
                'ib.purchase_order_id',
                DB::raw('CASE 
                WHEN ib.variant_id IS NOT NULL THEN CONCAT(p.name, " - ", pv.name) 
                ELSE p.name 
            END AS full_product_name'),
                'ib.initial_quantity',
                'ib.quantity AS remaining_quantity',
                'ib.price',
                'ib.publish'
            ])
            ->get();
    }
    public function getInventoryWithProduct()
    {
        return DB::table('inventory_batches AS ib')
            ->join('products AS p', 'p.id', '=', 'ib.product_id')
            ->leftJoin('product_variants AS pv', 'pv.id', '=', 'ib.variant_id')
            ->groupBy('ib.product_id', 'ib.variant_id', 'p.name', 'pv.name')
            ->having(DB::raw('SUM(ib.quantity)'), '<', 100)
            ->select([
                'ib.product_id',
                'ib.variant_id',
                DB::raw('CASE 
                WHEN ib.variant_id IS NOT NULL THEN CONCAT(p.name, " - ", pv.name) 
                ELSE p.name 
            END AS full_product_name'),
                DB::raw('SUM(ib.initial_quantity) AS total_initial_quantity'),
                DB::raw('SUM(ib.quantity) AS total_remaining_quantity'),
                'ib.publish'
            ])
            ->limit(10)
            ->get();
    }
    public function getInventoryDetails($productId, $variantId = null)
    {
        $inventoryDetails = DB::table('inventory_batches AS ib')
            ->join('products AS p', 'p.id', '=', 'ib.product_id') // Kết nối với bảng products
            ->leftJoin('product_variants AS pv', 'pv.id', '=', 'ib.variant_id') // Kết nối với bảng product_variants
            ->join('purchase_orders AS po', 'po.id', '=', 'ib.purchase_order_id') // Kết nối với bảng purchase_orders để lấy purchase_code
            ->where('ib.product_id', $productId) // Lọc theo product_id
            ->when($variantId !== null, function ($query) use ($variantId) {
                // Nếu variant_id có giá trị, lọc theo variant_id
                return $query->where('ib.variant_id', $variantId);
            })
            ->select([
                'po.code AS batch_id',
                'ib.id',
                'ib.initial_quantity',
                'ib.price',
                'ib.quantity AS remaining_quantity',
                'ib.created_at AS purchase_date',
                DB::raw('CONCAT(p.name, IFNULL(CONCAT(" - ", pv.name), "")) AS full_product_name'),
                'ib.publish',
                'p.price AS product_price',
                'pv.price AS variant_price'
            ])
            ->get()
            ->map(function ($item) use ($variantId) {
                if ($variantId === null) {
                    $item->product_name = $item->full_product_name;
                    $item->product_price = $item->product_price;
                } else {
                    $item->product_name = $item->full_product_name;
                    $item->product_price = $item->variant_price;
                }
                unset($item->variant_price);
                return $item;
            });


        if ($inventoryDetails->isNotEmpty()) {
            $firstItem = $inventoryDetails->first();
            $productDetails = [
                'product_name' => $firstItem->product_name,
                'product_price' => $firstItem->product_price,
                'details' => $inventoryDetails
            ];
            return $productDetails;
        }

        return null;
    }
    public function getInventoryWithTime($product_id, $startDate, $endDate)
    {

        $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay()->toDateTimeString();
        $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay()->toDateTimeString();

        $product = DB::table('products')->where('id', $product_id)->first(['id', 'name']);

        $variants = DB::table('product_variants')
            ->where('product_id', $product_id)
            ->get(['id', 'name']);

        if ($variants->isEmpty()) {
            $variants = collect([(object) ['id' => null, 'name' => 'Không có biến thể']]);
        }

        $variantIds = $variants->pluck('id')->toArray();

        $movements = DB::table('stock_movements')
            ->where('product_id', $product_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function ($query) use ($variantIds) {
                if (in_array(null, $variantIds, true)) {
                    $query->whereNull('variant_id')->orWhereIn('variant_id', $variantIds);
                } else {
                    $query->whereIn('variant_id', $variantIds);
                }
            })
            ->orderBy('created_at', 'asc')
            ->get(['type', 'quantity', 'variant_id', 'reference_id', 'reference_type', 'user_id', 'created_at']);

        $inventoryBatches = DB::table('inventory_batches')
            ->where('product_id', $product_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function ($query) use ($variantIds) {
                if (in_array(null, $variantIds, true)) {
                    $query->whereNull('variant_id')->orWhereIn('variant_id', $variantIds);
                } else {
                    $query->whereIn('variant_id', $variantIds);
                }
            })
            ->get(['variant_id', 'initial_quantity', 'quantity', 'price', 'purchase_order_id', 'created_at']);

        $groupedMovements = $movements->groupBy('variant_id');
        $groupedBatches = $inventoryBatches->groupBy('variant_id');

        $inventoryData = $variants->map(function ($variant) use ($groupedMovements, $groupedBatches) {
            $variantMovements = $groupedMovements[$variant->id] ?? collect();
            $variantBatches = $groupedBatches[$variant->id] ?? collect();
            $total_import  = $variantMovements->where('type', 'import')->sum('quantity');
            $total_export  = $variantMovements->where('type', 'export')->sum('quantity');
            $total_return  = $variantMovements->where('type', 'return')->sum('quantity');
            $total_initial_quantity = $variantBatches->sum('initial_quantity');
            $total_current_quantity = $variantBatches->sum('quantity');
            $expected_stock = $total_import - $total_export + $total_return;
            $missing_stock =  $total_current_quantity - $expected_stock;

            return [
                'variant_id'    => $variant->id,
                'variant_name'  => $variant->name,
                'total_import'  => $total_import,
                'total_export'  => $total_export,
                'total_return'  => $total_return,

                'total_initial_quantity' => $total_initial_quantity,
                'total_current_quantity' => $total_current_quantity,

                'expected_stock' => $expected_stock,
                'missing_stock'  => $missing_stock,

                'batch_data'    => $variantBatches->map(function ($batch) {
                    return [
                        'initial_quantity' => $batch->initial_quantity,
                        'current_quantity' => $batch->quantity,
                        'price'            => $batch->price,
                        'purchase_order_id' => $batch->purchase_order_id,
                        'created_at'       => $batch->created_at,
                    ];
                }),
                'movements'     => $variantMovements
            ];
        });

        $data = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variants' => $inventoryData,
        ];

        return $data;
    }


    public function getReport($catalogue_id, $startDate, $endDate)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay()->toDateTimeString();
        $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay()->toDateTimeString();

        $products = DB::table('products')
            ->where('product_catalogue_id', $catalogue_id)
            ->whereNull('deleted_at')
            ->get(['id', 'name', 'price']);

        $data = [];

        foreach ($products as $product) {
            $variants = DB::table('product_variants')
                ->where('product_id', $product->id)
                ->whereNull('deleted_at')
                ->get(['id', 'name']);
            if ($variants->isEmpty()) {
                $variants = collect([(object) ['id' => null, 'name' => $product->name]]);
            }

            $latestBatch = DB::table('inventory_batches')
                ->where('product_id', $product->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $purchase_price = $latestBatch ? $latestBatch->price : 0;
            $sale_price = $product->price;

            // Nếu có biến thể, dùng giá và tên của biến thể
            foreach ($variants as $variant) {
                if ($variant->id !== null) {
                    $variantPrice = DB::table('product_variants')->where('id', $variant->id)->value('price');
                    $sale_price = $variantPrice ?? $sale_price;  // Sử dụng giá biến thể nếu có
                    $product_name = $product->name . ' ' . $variant->name;
                } else {
                    $product_name = $product->name;  // Không có biến thể, chỉ dùng tên sản phẩm
                }
            }

            $stock_quantity = DB::table('inventory_batches')
                ->where('product_id', $product->id)
                ->when($variant->id, function ($query) use ($variant) {
                    return $query->where('variant_id', $variant->id);
                })
                ->sum('quantity');

            $total_capital = $latestBatch ? $purchase_price * $latestBatch->quantity : 0;

            // Số lượng tồn kho (lấy từ inventory_batches)
            $stock_quantity = $latestBatch ? $latestBatch->quantity : 0;

            // Lấy chi tiết lô từ inventory_batches
            $batchDetails = DB::table('inventory_batches')
                ->where('product_id', $product->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Lấy thông tin đơn hàng mua liên quan đến từng lô
            foreach ($batchDetails as &$batch) {
                $purchaseOrder = DB::table('purchase_orders')->where('id', $batch->purchase_order_id)->first();
                $batch->purchase_order_code = $purchaseOrder ? $purchaseOrder->code : null;
            }

            // Thêm dữ liệu vào mảng kết quả
            $data[] = [
                'product_id' => $product->id,
                'product_name' => $product_name,
                'sale_price' => $sale_price,
                'purchase_price' => $purchase_price,
                'total_capital' => $total_capital,
                'stock_quantity' => $stock_quantity,
                'details' => $batchDetails->map(function ($batch) {
                    return [
                        'batch_id' => $batch->id,
                        'purchase_order_code' => $batch->purchase_order_code,
                        'quantity' => $batch->quantity,
                        'initial_quantity' => $batch->initial_quantity,
                        'price' => $batch->price,
                        'publish' => $batch->publish,
                    ];
                }),
            ];
        }
        Log::info("GETREPORT", $data);
        return $data;
    }
    public function getCodeInventory()
    {
        // Lấy tất cả các batch inventory, bao gồm các sản phẩm và biến thể
        $inventoryBatches = InventoryBatch::with(['products', 'variant'])
            ->orderByDesc('created_at')
            ->get();

        $result = [];

        foreach ($inventoryBatches as $batch) {
            $code = $batch->code; // Lấy mã batch

            // Nếu batch chưa tồn tại trong kết quả, thêm vào
            if (!isset($result[$code])) {
                $result[$code] = [
                    'code' => $code,
                    'products' => []
                ];
            }

            $productId = $batch->product_id;
            $productName = $batch->products ? $batch->products->name : 'Không có sản phẩm';

            // Nếu sản phẩm chưa có trong danh sách, thêm vào (chỉ khi không có biến thể)
            if (!isset($result[$code]['products'][$productId])) {
                $result[$code]['products'][$productId] = [
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'variant' => [],
                    'initial_quantity' => null,
                    'quantity' => null,
                    'price' => null,
                    'batch_id' => null // Mặc định batch_id là null, sẽ cập nhật sau
                ];
            }

            // Nếu có biến thể, thêm thông tin biến thể vào
            if ($batch->variant_id) {
                $result[$code]['products'][$productId]['variant'][] = [
                    'variant_id' => $batch->variant_id,
                    'variant_name' => optional($batch->variant)->name ?? 'Không có biến thể',
                    'initial_quantity' => $batch->initial_quantity,
                    'quantity' => $batch->quantity,
                    'price' => $batch->price,
                    'batch_id' => $batch->id, // Thêm batch_id cho biến thể
                ];
            } else {
                // Nếu không có biến thể, lưu trữ thông tin về sản phẩm và batch_id
                $result[$code]['products'][$productId]['initial_quantity'] = $batch->initial_quantity;
                $result[$code]['products'][$productId]['quantity'] = $batch->quantity;
                $result[$code]['products'][$productId]['price'] = $batch->price;
                $result[$code]['products'][$productId]['batch_id'] = $batch->id; // Lưu batch_id vào sản phẩm không có biến thể
            }
        }

        // Chuyển đổi từ dạng associative array sang mảng tuần tự cho products
        foreach ($result as $code => &$batch) {
            $batch['products'] = array_values($batch['products']);
        }

        return $result;
    }
}
