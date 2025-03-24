<?php

namespace App\Repositories;

use App\Models\InventoryBatch;
use App\Repositories\Interfaces\InventoryBatchRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                'ib.id',  // ID của batch trong kho
                'ib.purchase_order_id',  // ID của phiếu nhập
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
            ->join('products AS p', 'p.id', '=', 'ib.product_id') // Kết nối với bảng products
            ->leftJoin('product_variants AS pv', 'pv.id', '=', 'ib.variant_id') // Kết nối với bảng product_variants
            ->groupBy('ib.product_id', 'ib.variant_id', 'p.name', 'pv.name') // Nhóm theo product_id và variant_id
            ->having(DB::raw('SUM(ib.quantity)'), '<', 100) // Lọc những sản phẩm có tổng số lượng còn lại bé hơn 20
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
                'ib.id', // Mã lô hàng
                'ib.initial_quantity', // Số lượng nhập
                'ib.price',
                'ib.quantity AS remaining_quantity', // Số lượng còn lại
                'ib.created_at AS purchase_date', // Ngày nhập
                DB::raw('CONCAT(p.name, IFNULL(CONCAT(" - ", pv.name), "")) AS full_product_name'),
                'ib.publish', // Trạng thái publish
                'p.price AS product_price', // Giá sản phẩm từ bảng products
                'pv.price AS variant_price' // Giá sản phẩm từ bảng product_variants (nếu có)
            ])
            ->get()
            ->map(function ($item) use ($variantId) {
                // Kiểm tra nếu variant_id là null thì sử dụng giá từ bảng products, còn nếu không thì lấy giá từ bảng product_variants
                if ($variantId === null) {
                    $item->product_name = $item->full_product_name;
                    $item->product_price = $item->product_price;
                } else {
                    $item->product_name = $item->full_product_name; // Tên sản phẩm cộng với tên biến thể
                    $item->product_price = $item->variant_price; // Giá từ bảng product_variants
                }
                unset($item->variant_price); // Loại bỏ variant_price nếu không sử dụng
                return $item;
            });

        // Nếu có dữ liệu, nhóm lại theo thông tin sản phẩm
        if ($inventoryDetails->isNotEmpty()) {
            $firstItem = $inventoryDetails->first(); // Lấy sản phẩm đầu tiên để truy xuất tên và giá
            $productDetails = [
                'product_name' => $firstItem->product_name, // Tên sản phẩm
                'product_price' => $firstItem->product_price, // Giá sản phẩm
                'details' => $inventoryDetails // Thông tin chi tiết các lô hàng
            ];
            return $productDetails;
        }

        return null; // Trả về null nếu không có dữ liệu
    }
    public function getInventoryWithTime($product_id, $startDate, $endDate)
    {
        // Chuyển đổi ngày từ d/m/Y thành format chuẩn
        $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay()->toDateTimeString();
        $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay()->toDateTimeString();

        // Lấy thông tin sản phẩm
        $product = DB::table('products')->where('id', $product_id)->first(['id', 'name']);

        // Lấy danh sách biến thể (nếu không có thì tạo object mặc định)
        $variants = DB::table('product_variants')
            ->where('product_id', $product_id)
            ->get(['id', 'name']);

        if ($variants->isEmpty()) {
            $variants = collect([(object) ['id' => null, 'name' => 'Không có biến thể']]);
        }

        $variantIds = $variants->pluck('id')->toArray();

        // Lấy dữ liệu từ bảng stock_movements
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

        // Lấy dữ liệu từ bảng inventory_batches
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

        // Gom nhóm dữ liệu theo variant_id
        $groupedMovements = $movements->groupBy('variant_id');
        $groupedBatches = $inventoryBatches->groupBy('variant_id');

        // Xử lý dữ liệu tồn kho theo từng biến thể
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

                // Tổng số lượng từ inventory_batches
                'total_initial_quantity' => $total_initial_quantity,
                'total_current_quantity' => $total_current_quantity,

                // Chênh lệch tồn kho
                'expected_stock' => $expected_stock,
                'missing_stock'  => $missing_stock, // Nếu > 0 thì mất hàng

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


        // Dữ liệu trả về
        $data = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variants' => $inventoryData,
        ];

        Log::info('Inventory Data:', $data);

        return $data;
    }


    public function getReport($catalogue_id, $startDate, $endDate)
    {
        // Chuyển đổi ngày từ d/m/Y thành định dạng chuẩn
        $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay()->toDateTimeString();
        $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay()->toDateTimeString();

        // Lấy dữ liệu sản phẩm
        $products = DB::table('products')
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

            // Lấy lô mới nhất để lấy giá và số lượng
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

            // Tổng vốn (giá * số lượng tồn kho hiện tại)
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
}
