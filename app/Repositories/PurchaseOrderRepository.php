<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserService
 * @package App\Services
 */
class PurchaseOrderRepository extends BaseRepository implements PurchaseOrderRepositoryInterface
{
    protected $model;
    protected $productVariantRepository;
    public function __construct(
        ProductVariantRepository $productVariantRepository,
        PurchaseOrder $model
    ) {
        $this->model = $model;
        $this->productVariantRepository = $productVariantRepository;
    }
    public function find($id)
    {
        return PurchaseOrder::find($id);  // Tìm đơn hàng theo ID sử dụng Eloquent
    }

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 1,
        array $extend = [],
        array $purchaseOrderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = [],
        // int $currentPage = 1,
    ) {
        $query = $this->model->select($column);
        return $query
            ->keyword(
                $condition['keyword'] ?? null,
                ['suppliers.name']
            )
            ->publish($condition['publish'] ?? null)
            ->customDropdownFilter($condition['dropdown'] ?? null)
            ->relationCount($relations ?? null)
            ->CustomWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->customJoin($join ?? null)
            ->customGroupBy($extend['groupBy'] ?? null)
            ->customerCreatedAt($condition['created_at'] ?? null)
            ->paginate($perPage)
            ->withQueryString()->withPath(env('APP_URL') . $extend['path']);
    }


    public function getPurchaseOrderById($id)
    {
        return $this->model
            ->select([
                'purchase_orders.*',
                'suppliers.name as supplier_name',
            ])
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->with(['purchase_order_details' => function ($query) {
                $query->select([
                    'purchase_order_details.id',
                    'purchase_order_details.purchase_order_id',
                    'purchase_order_details.product_id',
                    'products.name as product_name',
                    DB::raw('SUM(purchase_order_details.quantity) as total_quantity'),
                    DB::raw('AVG(purchase_order_details.price) as avg_price'),
                    DB::raw('SUM(purchase_order_details.subtotal) as total_price')
                ])
                    ->leftJoin('products', 'purchase_order_details.product_id', '=', 'products.id')
                    ->groupBy('purchase_order_details.product_id'); // Nhóm theo sản phẩm
            }])
            ->find($id);
    }




    // public function getPurchaseOrdersByCustomer($customerId)
    // {
    //     $purchaseOrders = DB::table('purchaseOrders')
    //         ->select(
    //             'purchaseOrders.id as purchaseOrder_id',
    //             'purchaseOrders.created_at',
    //             'purchaseOrders.address',
    //             'purchaseOrders.fullname',
    //             'purchaseOrders.phone',
    //             'purchaseOrders.email',
    //             'purchaseOrders.province_id',
    //             'purchaseOrders.district_id',
    //             'purchaseOrders.ward_id',
    //             'purchaseOrders.method',
    //             'purchaseOrders.confirm',
    //             'purchaseOrders.payment',
    //             'purchaseOrders.delivery',
    //             'purchaseOrders.shipping',
    //             'purchaseOrders.deleted_at',
    //             'provinces.name as province_name',
    //             'districts.name as district_name',
    //             'wards.name as ward_name',
    //             'purchaseOrder_product.price',
    //             'purchaseOrder_product.priceOriginal',
    //             'purchaseOrder_product.qty',
    //             'purchaseOrder_product.name as product_name',
    //             'purchaseOrder_product.option',
    //             'products.code as product_code',
    //             'products.id as product_id',
    //             'products.image as product_image'
    //         )
    //         ->where('purchaseOrders.customer_id', $customerId)
    //         ->leftJoin('provinces', 'purchaseOrders.province_id', '=', 'provinces.code')
    //         ->leftJoin('districts', 'purchaseOrders.district_id', '=', 'districts.code')
    //         ->leftJoin('wards', 'purchaseOrders.ward_id', '=', 'wards.code')
    //         ->leftJoin('purchaseOrder_product', 'purchaseOrder_product.purchaseOrder_id', '=', 'purchaseOrders.id')
    //         ->leftJoin('products', 'products.id', '=', 'purchaseOrder_product.product_id')
    //         ->get()
    //         ->groupBy('purchaseOrder_id'); // Nhóm theo purchaseOrder_id

    //     return $purchaseOrders->map(function ($purchaseOrder) {
    //         $purchaseOrderDetails = [
    //             'purchaseOrder_id' => $purchaseOrder[0]->purchaseOrder_id,
    //             'created_at' => $purchaseOrder[0]->created_at,
    //             'address' => $purchaseOrder[0]->address,
    //             'fullname' => $purchaseOrder[0]->fullname,
    //             'phone' => $purchaseOrder[0]->phone,
    //             'email' => $purchaseOrder[0]->email,
    //             'province_id' => $purchaseOrder[0]->province_id,
    //             'district_id' => $purchaseOrder[0]->district_id,
    //             'ward_id' => $purchaseOrder[0]->ward_id,
    //             'method' => $purchaseOrder[0]->method,
    //             'confirm' => $purchaseOrder[0]->confirm,
    //             'payment' => $purchaseOrder[0]->payment,
    //             'delivery' => $purchaseOrder[0]->delivery,
    //             'shipping' => $purchaseOrder[0]->shipping,
    //             'deleted_at' => $purchaseOrder[0]->deleted_at,
    //             'province_name' => $purchaseOrder[0]->province_name,
    //             'district_name' => $purchaseOrder[0]->district_name,
    //             'ward_name' => $purchaseOrder[0]->ward_name,
    //         ];

    //         // Xử lý sản phẩm
    //         $purchaseOrderDetails['products'] = $purchaseOrder->map(function ($product) {
    //             $productDetails = [
    //                 'price' => $product->price,
    //                 'priceOriginal' => $product->priceOriginal,
    //                 'qty' => $product->qty,
    //                 'product_name' => $product->product_name,
    //                 'option' => $product->option,
    //                 'product_code' => $product->product_code,
    //                 'product_id' => $product->product_id,
    //                 'product_image' => $product->product_image,
    //                 'variant_name' => null, // Mặc định là null, sẽ cập nhật sau
    //             ];

    //             // 🔥 Giải mã option JSON và tạo option_code
    //             $option = json_decode($product->option, true);

    //             if (isset($option['attribute']) && is_array($option['attribute'])) {
    //                 sort($option['attribute']); // Sắp xếp tăng dần
    //                 $option_code = implode(',', $option['attribute']); // Ghép thành chuỗi "8,10" hoặc "10,8"

    //                 // 🔥 Tìm variant_name từ bảng product_variants
    //                 $variant = DB::table('product_variants')
    //                     ->where('product_id', $product->product_id)
    //                     ->where('code', $option_code) // So khớp với `code` trong `product_variants`
    //                     ->first();

    //                 if ($variant) {
    //                     $productDetails['variant_name'] = $variant->name;
    //                 }
    //             }

    //             return $productDetails;
    //         });

    //         return $purchaseOrderDetails;
    //     });
    // }

    // public function getPurchaseOrdersByStatus($confirm = "", $payment = "", $delivery = "")
    // {
    //     // Lấy thông tin khách hàng đang đăng nhập
    //     $customer = Auth::guard('customer')->user();

    //     if (!$customer) {
    //         return response()->json(['error' => 'Chưa đăng nhập'], 401);
    //     }

    //     // Query cơ bản
    //     $query = DB::table('purchaseOrders')
    //         ->select(
    //             'purchaseOrders.id as purchaseOrder_id',
    //             'purchaseOrders.created_at',
    //             'purchaseOrders.address',
    //             'purchaseOrders.fullname',
    //             'purchaseOrders.phone',
    //             'purchaseOrders.email',
    //             'purchaseOrders.province_id',
    //             'purchaseOrders.district_id',
    //             'purchaseOrders.ward_id',
    //             'purchaseOrders.method',
    //             'purchaseOrders.confirm',
    //             'purchaseOrders.payment',
    //             'purchaseOrders.delivery',
    //             'purchaseOrders.shipping',
    //             'purchaseOrders.deleted_at',
    //             'provinces.name as province_name',
    //             'districts.name as district_name',
    //             'wards.name as ward_name',
    //             'purchaseOrder_product.price',
    //             'purchaseOrder_product.priceOriginal',
    //             'purchaseOrder_product.qty',
    //             'purchaseOrder_product.name as product_name',
    //             'purchaseOrder_product.option',
    //             'products.code as product_code',
    //             'products.id as product_id',
    //             'products.image as product_image'
    //         )
    //         ->where('purchaseOrders.customer_id', $customer->id)
    //         ->leftJoin('provinces', 'purchaseOrders.province_id', '=', 'provinces.code')
    //         ->leftJoin('districts', 'purchaseOrders.district_id', '=', 'districts.code')
    //         ->leftJoin('wards', 'purchaseOrders.ward_id', '=', 'wards.code')
    //         ->leftJoin('purchaseOrder_product', 'purchaseOrder_product.purchaseOrder_id', '=', 'purchaseOrders.id')
    //         ->leftJoin('products', 'products.id', '=', 'purchaseOrder_product.product_id');

    //     // Nếu có giá trị truyền vào thì lọc theo trạng thái
    //     if (!empty($confirm)) {
    //         $query->where('purchaseOrders.confirm', $confirm);
    //     }
    //     if (!empty($payment)) {
    //         $query->where('purchaseOrders.payment', $payment);
    //     }
    //     if (!empty($delivery)) {
    //         $query->where('purchaseOrders.delivery', $delivery);
    //     }

    //     $purchaseOrders = $query->get()->groupBy('purchaseOrder_id'); // Nhóm theo purchaseOrder_id

    //     // Xử lý dữ liệu trả về
    //     return $purchaseOrders->map(function ($purchaseOrder) {
    //         $purchaseOrderDetails = [
    //             'purchaseOrder_id' => $purchaseOrder[0]->purchaseOrder_id,
    //             'created_at' => $purchaseOrder[0]->created_at,
    //             'address' => $purchaseOrder[0]->address,
    //             'fullname' => $purchaseOrder[0]->fullname,
    //             'phone' => $purchaseOrder[0]->phone,
    //             'email' => $purchaseOrder[0]->email,
    //             'province_id' => $purchaseOrder[0]->province_id,
    //             'district_id' => $purchaseOrder[0]->district_id,
    //             'ward_id' => $purchaseOrder[0]->ward_id,
    //             'method' => $purchaseOrder[0]->method,
    //             'confirm' => $purchaseOrder[0]->confirm,
    //             'payment' => $purchaseOrder[0]->payment,
    //             'delivery' => $purchaseOrder[0]->delivery,
    //             'shipping' => $purchaseOrder[0]->shipping,
    //             'deleted_at' => $purchaseOrder[0]->deleted_at,
    //             'province_name' => $purchaseOrder[0]->province_name,
    //             'district_name' => $purchaseOrder[0]->district_name,
    //             'ward_name' => $purchaseOrder[0]->ward_name,
    //         ];

    //         // Xử lý danh sách sản phẩm trong đơn
    //         $purchaseOrderDetails['products'] = $purchaseOrder->map(function ($product) {
    //             $productDetails = [
    //                 'price' => $product->price,
    //                 'priceOriginal' => $product->priceOriginal,
    //                 'qty' => $product->qty,
    //                 'product_name' => $product->product_name,
    //                 'option' => $product->option,
    //                 'product_code' => $product->product_code,
    //                 'product_id' => $product->product_id,
    //                 'product_image' => $product->product_image,
    //                 'variant_name' => null, // Mặc định là null, sẽ cập nhật sau
    //             ];

    //             // 🔥 Giải mã option JSON và tạo option_code
    //             $option = json_decode($product->option, true);

    //             if (isset($option['attribute']) && is_array($option['attribute'])) {
    //                 sort($option['attribute']); // Sắp xếp tăng dần
    //                 $option_code = implode(',', $option['attribute']); // Ghép thành chuỗi "8,10" hoặc "10,8"

    //                 // 🔥 Tìm variant_name từ bảng product_variants
    //                 $variant = DB::table('product_variants')
    //                     ->where('product_id', $product->product_id)
    //                     ->where('code', $option_code) // So khớp với `code` trong `product_variants`
    //                     ->first();

    //                 if ($variant) {
    //                     $productDetails['variant_name'] = $variant->name;
    //                 }
    //             }

    //             return $productDetails;
    //         });
    //         Log::info("purchaseOrder", ['purchaseOrder', $purchaseOrderDetails]);

    //         return $purchaseOrderDetails;
    //     });
    // }




    // public function getPurchaseOrderByPurchaseOrderId($purchaseOrderId)
    // {
    //     // Truy vấn đơn hàng
    //     $purchaseOrder = DB::table('purchaseOrders')
    //         ->select(
    //             'purchaseOrders.id as purchaseOrder_id',
    //             'purchaseOrders.created_at',
    //             'purchaseOrders.address',
    //             'purchaseOrders.fullname',
    //             'purchaseOrders.phone',
    //             'purchaseOrders.email',
    //             'purchaseOrders.province_id',
    //             'purchaseOrders.district_id',
    //             'purchaseOrders.ward_id',
    //             'purchaseOrders.method',
    //             'purchaseOrders.confirm',
    //             'purchaseOrders.payment',
    //             'purchaseOrders.delivery',
    //             'purchaseOrders.shipping',
    //             'purchaseOrders.deleted_at',
    //             'provinces.name as province_name',
    //             'districts.name as district_name',
    //             'wards.name as ward_name'
    //         )
    //         ->leftJoin('provinces', 'purchaseOrders.province_id', '=', 'provinces.code')
    //         ->leftJoin('districts', 'purchaseOrders.district_id', '=', 'districts.code')
    //         ->leftJoin('wards', 'purchaseOrders.ward_id', '=', 'wards.code')
    //         ->where('purchaseOrders.id', $purchaseOrderId)
    //         ->first(); // Lấy một bản ghi duy nhất

    //     // Kiểm tra nếu không tìm thấy đơn hàng
    //     if (!$purchaseOrder) {
    //         return null;
    //     }

    //     // Truy vấn sản phẩm của đơn hàng
    //     $products = DB::table('purchaseOrder_product')
    //         ->select(
    //             'purchaseOrder_product.price',
    //             'purchaseOrder_product.qty',
    //             'purchaseOrder_product.name as product_name',
    //             'purchaseOrder_product.option',
    //             'products.code as product_code',
    //             'products.id as product_id',
    //             'products.image as product_image'  // Thêm trường image
    //         )
    //         ->leftJoin('products', 'products.id', '=', 'purchaseOrder_product.product_id')
    //         ->where('purchaseOrder_product.purchaseOrder_id', $purchaseOrderId)
    //         ->get();

    //     // Tạo mảng chứa thông tin đơn hàng
    //     $purchaseOrderDetails = [
    //         'purchaseOrder_id' => $purchaseOrder->purchaseOrder_id,
    //         'created_at' => $purchaseOrder->created_at,
    //         'address' => $purchaseOrder->address,
    //         'fullname' => $purchaseOrder->fullname,
    //         'phone' => $purchaseOrder->phone,
    //         'email' => $purchaseOrder->email,
    //         'province_id' => $purchaseOrder->province_id,
    //         'district_id' => $purchaseOrder->district_id,
    //         'ward_id' => $purchaseOrder->ward_id,
    //         'method' => $purchaseOrder->method,
    //         'confirm' => $purchaseOrder->confirm,
    //         'payment' => $purchaseOrder->payment,
    //         'delivery' => $purchaseOrder->delivery,
    //         'shipping' => $purchaseOrder->shipping,
    //         'deleted_at' => $purchaseOrder->deleted_at,
    //         'province_name' => $purchaseOrder->province_name,
    //         'district_name' => $purchaseOrder->district_name,
    //         'ward_name' => $purchaseOrder->ward_name,
    //     ];

    //     // Thêm thông tin sản phẩm vào mảng
    //     $purchaseOrderDetails['products'] = $products->map(function ($product) {
    //         return [
    //             'price' => $product->price,
    //             'qty' => $product->qty,
    //             'product_name' => $product->product_name,
    //             'option' => $product->option,
    //             'product_code' => $product->product_code,
    //             'product_id' => $product->product_id,
    //             'product_image' => $product->product_image,  // Thêm thuộc tính image
    //         ];
    //     });

    //     return $purchaseOrderDetails;
    // }

    // public function getPurchaseOrderByTime($month, $year)
    // {
    //     return $this->model
    //         ->whereMonth('created_at', $month)
    //         ->whereYear('created_at', $year)
    //         ->count();
    // }

    // public function getTotalPurchaseOrders()
    // {
    //     return $this->model->count();
    // }

    // public function getCanclePurchaseOrders()
    // {
    //     return $this->model->where('confirm', '=', 'cancle')->count();
    // }

    // public function revenuePurchaseOrders()
    // {

    //     return $this->model
    //         ->join('purchaseOrder_product', 'purchaseOrder_product.purchaseOrder_id', '=', 'purchaseOrders.id')
    //         ->where('purchaseOrders.payment', '=', 'paid')
    //         ->sum(DB::raw('purchaseOrder_product.price * purchaseOrder_product.qty'));
    // }

    // public function checkUserHasPurchaseOrderForProduct($userId, $productId)
    // {
    //     if ($userId) {
    //         // Kiểm tra xem người dùng đã có đơn hàng với sản phẩm cụ thể hay chưa
    //         $hasPurchaseOrder = $this->model
    //             ->join('purchaseOrder_product', 'purchaseOrder_product.purchaseOrder_id', '=', 'purchaseOrders.id') // Kết nối bảng purchaseOrders và purchaseOrder_product
    //             ->where('purchaseOrders.customer_id', $userId) // Lọc theo customer_id
    //             ->where('purchaseOrder_product.product_id', $productId) // Lọc theo product_id
    //             ->whereNull('purchaseOrders.deleted_at') // Lọc theo trường deleted_at trong bảng purchaseOrders (nếu cần)
    //             ->exists(); // Kiểm tra sự tồn tại của đơn hàng

    //         return $hasPurchaseOrder; // Trả về true nếu có đơn hàng, false nếu không
    //     } else {
    //         return false; // Nếu không có userId, trả về false
    //     }
    // }

    // public function revenueByYear($year)
    // {
    //     return $this->model->select(
    //         DB::raw('
    //             months.month, 
    //             COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(purchaseOrders.cart, "$.cartTotal"))), 0) as monthly_revenue
    //         ')
    //     )
    //         ->from(DB::raw('(
    //         SELECT 1 AS month
    //             UNION SELECT 2
    //             UNION SELECT 3
    //             UNION SELECT 4
    //             UNION SELECT 5
    //             UNION SELECT 6
    //             UNION SELECT 7
    //             UNION SELECT 8
    //             UNION SELECT 9
    //             UNION SELECT 10
    //             UNION SELECT 11
    //             UNION SELECT 12
    //     ) as months'))
    //         ->leftJoin('purchaseOrders', function ($join) use ($year) {
    //             $join->on(DB::raw('months.month'), '=', DB::raw('MONTH(purchaseOrders.created_at)'))
    //                 ->where('purchaseOrders.payment', '=', 'paid')
    //                 ->where(DB::raw('YEAR(purchaseOrders.created_at)'), '=', $year);
    //         })
    //         ->groupBy('months.month')
    //         ->get();
    // }

    // public function revenue7Day()
    // {
    //     return $this->model
    //         ->select(DB::raw('
    //         dates.date,
    //         COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(purchaseOrders.cart, "$.cartTotal"))), 0) as daily_revenue
    //     '))
    //         ->from(DB::raw('(
    //         SELECT CURDATE() - INTERVAL (a.a + (10*b.a) + (100 * c.a)) DAY as date
    //         FROM (
    //             SELECT 0 AS a UNION ALL
    //             SELECT 1 UNION ALL
    //             SELECT 2 UNION ALL
    //             SELECT 3 UNION ALL
    //             SELECT 4 UNION ALL
    //             SELECT 5 UNION ALL
    //             SELECT 6 UNION ALL
    //             SELECT 7 UNION ALL
    //             SELECT 8 UNION ALL
    //             SELECT 9
    //         ) as a
    //         CROSS JOIN (
    //             SELECT 0 AS a UNION ALL 
    //             SELECT 1 UNION ALL 
    //             SELECT 2 UNION ALL 
    //             SELECT 3 UNION ALL 
    //             SELECT 4 UNION ALL 
    //             SELECT 5 UNION ALL 
    //             SELECT 6 UNION ALL 
    //             SELECT 7 UNION ALL 
    //             SELECT 8 UNION ALL 
    //             SELECT 9
    //         ) as b
    //         CROSS JOIN (
    //             SELECT 0 AS a UNION ALL 
    //             SELECT 1 UNION ALL 
    //             SELECT 2 UNION ALL 
    //             SELECT 3 UNION ALL 
    //             SELECT 4 UNION ALL 
    //             SELECT 5 UNION ALL 
    //             SELECT 6 UNION ALL 
    //             SELECT 7 UNION ALL 
    //             SELECT 8 UNION ALL 
    //             SELECT 9
    //         ) as c
    //     ) as dates'))

    //         ->leftJoin('purchaseOrders', function ($join) {
    //             $join->on(DB::raw('DATE(purchaseOrders.created_at)'), '=', DB::raw('dates.date'))
    //                 ->where('purchaseOrders.payment', '=', 'paid');
    //         })
    //         ->where(DB::raw('dates.date'), '>=', DB::raw('CURDATE() - INTERVAL 6 DAY'))
    //         ->groupBy(DB::raw('dates.date'))
    //         ->purchaseOrderBy(DB::raw('dates.date'), 'ASC')
    //         ->get();
    // }

    // public function revenueCurrentMonth($currentMonth, $currentYear)
    // {
    //     return $this->model->select(
    //         DB::raw('DAY(created_at) as day'),
    //         DB::raw('COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(purchaseOrders.cart, "$.cartTotal"))), 0) as daily_revenue')
    //     )
    //         ->whereMonth('created_at', $currentMonth)
    //         ->whereYear('created_at', $currentYear)
    //         ->where('purchaseOrders.payment', '=', 'paid')
    //         ->groupBy('day')
    //         ->purchaseOrderBy('day')
    //         ->get()->toArray();
    // }

    // public function purchaseOrderByCustomer($customer_id = 0, $condition = [])
    // {
    //     $query = $this->model->select(
    //         [
    //             'purchaseOrders.*',
    //             'provinces.name as province_name',
    //             'districts.name as district_name',
    //             'wards.name as ward_name',
    //         ]
    //     )
    //         ->where('purchaseOrders.customer_id', $customer_id)
    //         ->leftJoin('provinces', 'purchaseOrders.province_id', '=', 'provinces.code')
    //         ->leftJoin('districts', 'purchaseOrders.district_id', '=', 'districts.code')
    //         ->leftJoin('wards', 'purchaseOrders.ward_id', '=', 'wards.code')
    //         ->with('products');
    //     if (isset($condition['keyword']) && !empty($condition['keyword'])) {
    //         $query->where(function ($query) use ($condition) {
    //             $keyword = $condition['keyword'];
    //             $query->where('purchaseOrders.code', 'LIKE', '%' . $keyword . '%')
    //                 ->orWhere('purchaseOrders.fullname', 'LIKE', '%' . $keyword . '%')
    //                 ->orWhere('purchaseOrders.phone', 'LIKE', '%' . $keyword . '%')
    //                 ->orWhere('purchaseOrders.address', 'LIKE', '%' . $keyword . '%');
    //         });
    //     }
    //     return $query->paginate(20);
    // }

    // /*Filer Time */

    // public function getReportTime($startDate, $endDate)
    // {
    //     return $this->model->select(
    //         DB::raw("DATE(created_at) as purchaseOrder_date"),
    //         DB::raw("COUNT(DISTINCT customer_id) as count_customer "),
    //         DB::raw("COUNT(DISTINCT purchaseOrders.id) as count_purchaseOrder"),
    //         DB::raw("SUM(purchaseOrder_product.price * purchaseOrder_product.qty) as sum_revenue"),
    //         DB::raw("(SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(promotion, '$.discount'))) FROM purchaseOrders WHERE DATE(created_at) = purchaseOrder_date GROUP BY DATE(created_at)) as sum_discount"),
    //     )
    //         ->join('purchaseOrder_product', 'purchaseOrder_product.purchaseOrder_id', '=', 'purchaseOrders.id')
    //         ->whereDate('created_at', '>=', $startDate)
    //         ->whereDate('created_at', '<=', $endDate)
    //         ->where('purchaseOrders.payment', '=', 'paid')
    //         ->groupBy('purchaseOrder_date')
    //         ->get()->toArray();
    // }


    // public function getProductReportTime($startDate, $endDate)
    // {
    //     return $this->model->select(
    //         DB::raw("IFNULL(product_variants.sku, products.code) as sku"),
    //         DB::raw("products.name as product_name"),
    //         DB::raw("COUNT(DISTINCT purchaseOrders.customer_id) as count_customer"),
    //         DB::raw("COUNT(purchaseOrders.id) as count_purchaseOrder"),
    //         DB::raw("SUM(purchaseOrder_product.price * purchaseOrder_product.qty) as sum_revenue"),
    //         DB::raw("(SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(promotion, '$.discount'))) FROM purchaseOrders WHERE DATE(created_at) = DATE(purchaseOrders.created_at)) as sum_discount")
    //     )
    //         ->join('purchaseOrder_product', 'purchaseOrder_product.purchaseOrder_id', '=', 'purchaseOrders.id')
    //         ->leftJoin('product_variants', 'product_variants.uuid', '=', 'purchaseOrder_product.uuid')
    //         ->leftJoin('products', 'products.id', '=', 'purchaseOrder_product.product_id')
    //         ->whereDate('purchaseOrders.created_at', '>=', $startDate)
    //         ->whereDate('purchaseOrders.created_at', '<=', $endDate)
    //         ->where('purchaseOrders.payment', '=', 'paid')
    //         ->groupBy('purchaseOrder_product.product_id')
    //         ->get()->toArray();
    // }

    // public function getCustomerReportTime($startDate, $endDate)
    // {
    //     return $this->model->select(
    //         DB::raw("sources.name as source_name"),
    //         DB::raw("COUNT(DISTINCT purchaseOrders.customer_id) as count_customer"),
    //         DB::raw("COUNT(purchaseOrders.id) as count_purchaseOrder"),
    //         DB::raw("SUM(purchaseOrder_product.price * purchaseOrder_product.qty) as sum_revenue"),
    //         DB::raw("(SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(promotion, '$.discount')))) as sum_discount")
    //     )
    //         ->join('customers', 'customers.id', '=', 'purchaseOrders.customer_id')
    //         ->join('purchaseOrder_product', 'purchaseOrder_product.purchaseOrder_id', '=', 'purchaseOrders.id')
    //         ->leftJoin('sources', 'sources.id', '=', 'customers.source_id')
    //         ->whereDate('purchaseOrders.created_at', '>=', $startDate)
    //         ->whereDate('purchaseOrders.created_at', '<=', $endDate)
    //         ->where('purchaseOrders.payment', '=', 'paid')
    //         ->groupBy('sources.id')
    //         ->get()->toArray();
    // }

    // public function getTotalRevenueReportTime($startDate, $endDate)
    // {
    //     return $this->model->select(
    //         DB::raw("SUM(purchaseOrder_product.price * purchaseOrder_product.qty) as sum_revenue"),
    //         DB::raw("(SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(promotion, '$.discount')))) as sum_discount")
    //     )
    //         ->join('purchaseOrder_product', 'purchaseOrder_product.purchaseOrder_id', '=', 'purchaseOrders.id')
    //         ->whereDate('purchaseOrders.created_at', '>=', $startDate)
    //         ->whereDate('purchaseOrders.created_at', '<=', $endDate)
    //         ->where('purchaseOrders.payment', '=', 'paid')
    //         ->get()
    //         ->toArray();
    // }

    // public function newPurchaseOrder($startDate, $endDate)
    // {
    //     return $this->model
    //         ->whereDate('purchaseOrders.created_at', '>=', $startDate)
    //         ->whereDate('purchaseOrders.created_at', '<=', $endDate)
    //         ->get();
    // }
}
