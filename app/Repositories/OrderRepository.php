<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
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
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected $model;
    protected $productVariantRepository;
    public function __construct(
        ProductVariantRepository $productVariantRepository,
        Order $model
    ) {
        $this->model = $model;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function pagination(
        array $column = ['orders.*'],
        array $condition = [],
        int $perPage = 1,
        array $extend = [],
        array $orderBy = ['orders.id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = []
    ) {
        $query = $this->model->select($column);

        // Lấy danh sách provinces, districts, wards
        $provinces = DB::table('provinces')->pluck('name', 'code')->toArray();
        $districts = DB::table('districts')->pluck('name', 'code')->toArray();
        $wards = DB::table('wards')->pluck('name', 'code')->toArray();

        $data = $query
            ->keyword($condition['keyword'] ?? null, ['fullname', 'phone', 'email', 'address', 'code'], ['field' => 'products.name', 'relation' => 'products'])
            ->publish($condition['publish'] ?? null)
            ->customDropdownFilter($condition['dropdown'] ?? null)
            ->relationCount($relations ?? null)
            ->CustomWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->customJoin($join ?? null)
            ->customGroupBy($extend['groupBy'] ?? null)
            ->customOrderBy($orderBy ?? null)
            ->customerCreatedAt($condition['created_at'] ?? null)
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(env('APP_URL') . ($extend['path'] ?? ''));

        $data->getCollection()->transform(function ($order) use ($provinces, $districts, $wards) {
            $order->province_name = $provinces[$order->province_id] ?? null;
            $order->district_name = $districts[$order->district_id] ?? null;
            $order->ward_name = $wards[$order->ward_id] ?? null;
            return $order;
        });

        return $data;
    }


    public function getOrderById($id)
    {
        return $this->model->select(
            [
                'orders.*',
                'provinces.name as province_name',
                'districts.name as district_name',
                'wards.name as ward_name',
            ]
        )
            ->leftJoin('provinces', 'orders.province_id', '=', 'provinces.code')
            ->leftJoin('districts', 'orders.district_id', '=', 'districts.code')
            ->leftJoin('wards', 'orders.ward_id', '=', 'wards.code')
            ->with('products')
            ->find($id);
    }
    public function getOrdersByCustomer($customerId)
    {
        $orders = DB::table('orders')
            ->select(
                'orders.id as order_id',
                'orders.created_at',
                'orders.address',
                'orders.fullname',
                'orders.phone',
                'orders.email',
                'orders.province_id',
                'orders.district_id',
                'orders.ward_id',
                'orders.method',
                'orders.confirm',
                'orders.payment',
                'orders.delivery',
                'orders.shipping',
                'orders.deleted_at',
                'provinces.name as province_name',
                'districts.name as district_name',
                'wards.name as ward_name',
                'order_product.price',
                'order_product.priceOriginal',
                'order_product.qty',
                'order_product.name as product_name',
                'order_product.option',
                'products.code as product_code',
                'products.id as product_id',
                'products.image as product_image'
            )
            ->where('orders.customer_id', $customerId)
            ->leftJoin('provinces', 'orders.province_id', '=', 'provinces.code')
            ->leftJoin('districts', 'orders.district_id', '=', 'districts.code')
            ->leftJoin('wards', 'orders.ward_id', '=', 'wards.code')
            ->leftJoin('order_product', 'order_product.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_product.product_id')
            ->get()
            ->groupBy('order_id'); // Nhóm theo order_id

        // Sắp xếp đơn hàng theo thời gian tạo (created_at) mới nhất
        $orders = $orders->sortByDesc(function ($order) {
            return $order[0]->created_at; // Sắp xếp theo created_at
        });

        return $orders->map(function ($order) {
            $orderDetails = [
                'order_id' => $order[0]->order_id,
                'created_at' => $order[0]->created_at,
                'address' => $order[0]->address,
                'fullname' => $order[0]->fullname,
                'phone' => $order[0]->phone,
                'email' => $order[0]->email,
                'province_id' => $order[0]->province_id,
                'district_id' => $order[0]->district_id,
                'ward_id' => $order[0]->ward_id,
                'method' => $order[0]->method,
                'confirm' => $order[0]->confirm,
                'payment' => $order[0]->payment,
                'delivery' => $order[0]->delivery,
                'shipping' => $order[0]->shipping,
                'deleted_at' => $order[0]->deleted_at,
                'province_name' => $order[0]->province_name,
                'district_name' => $order[0]->district_name,
                'ward_name' => $order[0]->ward_name,
            ];

            // Xử lý sản phẩm
            $orderDetails['products'] = $order->map(function ($product) {
                $productDetails = [
                    'price' => $product->price,
                    'priceOriginal' => $product->priceOriginal,
                    'qty' => $product->qty,
                    'product_name' => $product->product_name,
                    'option' => $product->option,
                    'product_code' => $product->product_code,
                    'product_id' => $product->product_id,
                    'product_image' => $product->product_image,
                    'variant_name' => null, // Mặc định là null, sẽ cập nhật sau
                ];

                // 🔥 Giải mã option JSON và tạo option_code
                $option = json_decode($product->option, true);

                if (isset($option['attribute']) && is_array($option['attribute'])) {
                    sort($option['attribute']); // Sắp xếp tăng dần
                    $option_code = implode(',', $option['attribute']); // Ghép thành chuỗi "8,10" hoặc "10,8"

                    // 🔥 Tìm variant_name từ bảng product_variants
                    $variant = DB::table('product_variants')
                        ->where('product_id', $product->product_id)
                        ->where('code', $option_code) // So khớp với `code` trong `product_variants`
                        ->first();

                    if ($variant) {
                        $productDetails['variant_name'] = $variant->name;
                    }
                }

                return $productDetails;
            });

            return $orderDetails;
        });
    }

    public function getOrdersByStatus($confirm = "", $payment = "", $delivery = "")
    {
        // Lấy thông tin khách hàng đang đăng nhập
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json(['error' => 'Chưa đăng nhập'], 401);
        }

        // Query cơ bản
        $query = DB::table('orders')
            ->select(
                'orders.id as order_id',
                'orders.created_at',
                'orders.address',
                'orders.fullname',
                'orders.phone',
                'orders.email',
                'orders.province_id',
                'orders.district_id',
                'orders.ward_id',
                'orders.method',
                'orders.confirm',
                'orders.payment',
                'orders.delivery',
                'orders.shipping',
                'orders.deleted_at',
                'provinces.name as province_name',
                'districts.name as district_name',
                'wards.name as ward_name',
                'order_product.price',
                'order_product.priceOriginal',
                'order_product.qty',
                'order_product.name as product_name',
                'order_product.option',
                'products.code as product_code',
                'products.id as product_id',
                'products.image as product_image'
            )
            ->where('orders.customer_id', $customer->id)
            ->leftJoin('provinces', 'orders.province_id', '=', 'provinces.code')
            ->leftJoin('districts', 'orders.district_id', '=', 'districts.code')
            ->leftJoin('wards', 'orders.ward_id', '=', 'wards.code')
            ->leftJoin('order_product', 'order_product.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_product.product_id');

        // Nếu có giá trị truyền vào thì lọc theo trạng thái
        if (!empty($confirm)) {
            $query->where('orders.confirm', $confirm);
        }
        if (!empty($payment)) {
            $query->where('orders.payment', $payment);
        }
        if (!empty($delivery)) {
            $query->where('orders.delivery', $delivery);
        }

        $orders = $query->get()->groupBy('order_id');
        $orders = $orders->sortByDesc(function ($order) {
            return $order[0]->created_at;
        });

        return $orders->map(function ($order) {
            $orderDetails = [
                'order_id' => $order[0]->order_id,
                'created_at' => $order[0]->created_at,
                'address' => $order[0]->address,
                'fullname' => $order[0]->fullname,
                'phone' => $order[0]->phone,
                'email' => $order[0]->email,
                'province_id' => $order[0]->province_id,
                'district_id' => $order[0]->district_id,
                'ward_id' => $order[0]->ward_id,
                'method' => $order[0]->method,
                'confirm' => $order[0]->confirm,
                'payment' => $order[0]->payment,
                'delivery' => $order[0]->delivery,
                'shipping' => $order[0]->shipping,
                'deleted_at' => $order[0]->deleted_at,
                'province_name' => $order[0]->province_name,
                'district_name' => $order[0]->district_name,
                'ward_name' => $order[0]->ward_name,
            ];

            // Xử lý danh sách sản phẩm trong đơn
            $orderDetails['products'] = $order->map(function ($product) {
                $productDetails = [
                    'price' => $product->price,
                    'priceOriginal' => $product->priceOriginal,
                    'qty' => $product->qty,
                    'product_name' => $product->product_name,
                    'option' => $product->option,
                    'product_code' => $product->product_code,
                    'product_id' => $product->product_id,
                    'product_image' => $product->product_image,
                    'variant_name' => null, // Mặc định là null, sẽ cập nhật sau
                ];

                // 🔥 Giải mã option JSON và tạo option_code
                $option = json_decode($product->option, true);

                if (isset($option['attribute']) && is_array($option['attribute'])) {
                    sort($option['attribute']); // Sắp xếp tăng dần
                    $option_code = implode(',', $option['attribute']); // Ghép thành chuỗi "8,10" hoặc "10,8"

                    // 🔥 Tìm variant_name từ bảng product_variants
                    $variant = DB::table('product_variants')
                        ->where('product_id', $product->product_id)
                        ->where('code', $option_code) // So khớp với `code` trong `product_variants`
                        ->first();

                    if ($variant) {
                        $productDetails['variant_name'] = $variant->name;
                    }
                }

                return $productDetails;
            });
            Log::info("order", ['order', $orderDetails]);

            return $orderDetails;
        });
    }




    public function getOrderByOrderId($orderId)
    {
        // Truy vấn đơn hàng
        $order = DB::table('orders')
            ->select(
                'orders.id as order_id',
                'orders.created_at',
                'orders.address',
                'orders.fullname',
                'orders.phone',
                'orders.email',
                'orders.province_id',
                'orders.district_id',
                'orders.ward_id',
                'orders.method',
                'orders.confirm',
                'orders.payment',
                'orders.delivery',
                'orders.shipping',
                'orders.deleted_at',
                'provinces.name as province_name',
                'districts.name as district_name',
                'wards.name as ward_name'
            )
            ->leftJoin('provinces', 'orders.province_id', '=', 'provinces.code')
            ->leftJoin('districts', 'orders.district_id', '=', 'districts.code')
            ->leftJoin('wards', 'orders.ward_id', '=', 'wards.code')
            ->where('orders.id', $orderId)
            ->first(); // Lấy một bản ghi duy nhất

        // Kiểm tra nếu không tìm thấy đơn hàng
        if (!$order) {
            return null;
        }

        // Truy vấn sản phẩm của đơn hàng
        $products = DB::table('order_product')
            ->select(
                'order_product.price',
                'order_product.qty',
                'order_product.name as product_name',
                'order_product.option',
                'order_product.variant_id', // Thêm variant_id
                'order_product.batch_id',   // Thêm batch_id
                'products.code as product_code',
                'products.id as product_id',
                'products.image as product_image'
            )
            ->leftJoin('products', 'products.id', '=', 'order_product.product_id')
            ->where('order_product.order_id', $orderId)
            ->get();

        // Tạo mảng chứa thông tin đơn hàng
        $orderDetails = [
            'order_id' => $order->order_id,
            'created_at' => $order->created_at,
            'address' => $order->address,
            'fullname' => $order->fullname,
            'phone' => $order->phone,
            'email' => $order->email,
            'province_id' => $order->province_id,
            'district_id' => $order->district_id,
            'ward_id' => $order->ward_id,
            'method' => $order->method,
            'confirm' => $order->confirm,
            'payment' => $order->payment,
            'delivery' => $order->delivery,
            'shipping' => $order->shipping,
            'deleted_at' => $order->deleted_at,
            'province_name' => $order->province_name,
            'district_name' => $order->district_name,
            'ward_name' => $order->ward_name,
        ];

        // Thêm thông tin sản phẩm vào mảng
        $orderDetails['products'] = $products->map(function ($product) {
            return [
                'price' => $product->price,
                'qty' => $product->qty,
                'product_name' => $product->product_name,
                'option' => $product->option,
                'variant_id' => $product->variant_id,  // Thêm vào output
                'batch_id' => $product->batch_id,      // Thêm vào output
                'product_code' => $product->product_code,
                'product_id' => $product->product_id,
                'product_image' => $product->product_image,
            ];
        });

        return $orderDetails;
    }

    public function getOrderByTime($month, $year)
    {
        return $this->model
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();
    }

    public function getTotalOrders()
    {
        return $this->model->count();
    }

    public function getCancleOrders()
    {
        return $this->model->where('confirm', '=', 'cancle')->count();
    }

    public function revenueOrders()
    {

        return $this->model
            ->join('order_product', 'order_product.order_id', '=', 'orders.id')
            ->where('orders.payment', '=', 'paid')
            ->sum(DB::raw('order_product.price * order_product.qty'));
    }

    public function checkUserHasOrderForProduct($userId, $productId)
    {
        if ($userId) {
            // Kiểm tra xem người dùng đã có đơn hàng với sản phẩm cụ thể hay chưa
            $hasOrder = $this->model
                ->join('order_product', 'order_product.order_id', '=', 'orders.id') // Kết nối bảng orders và order_product
                ->where('orders.customer_id', $userId) // Lọc theo customer_id
                ->where('order_product.product_id', $productId) // Lọc theo product_id
                ->whereNull('orders.deleted_at') // Lọc theo trường deleted_at trong bảng orders (nếu cần)
                ->exists(); // Kiểm tra sự tồn tại của đơn hàng

            return $hasOrder; // Trả về true nếu có đơn hàng, false nếu không
        } else {
            return false; // Nếu không có userId, trả về false
        }
    }

    public function revenueByYear($year)
    {
        return $this->model->select(
            DB::raw('
                months.month, 
                COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(orders.cart, "$.cartTotal"))), 0) as monthly_revenue
            ')
        )
            ->from(DB::raw('(
            SELECT 1 AS month
                UNION SELECT 2
                UNION SELECT 3
                UNION SELECT 4
                UNION SELECT 5
                UNION SELECT 6
                UNION SELECT 7
                UNION SELECT 8
                UNION SELECT 9
                UNION SELECT 10
                UNION SELECT 11
                UNION SELECT 12
        ) as months'))
            ->leftJoin('orders', function ($join) use ($year) {
                $join->on(DB::raw('months.month'), '=', DB::raw('MONTH(orders.created_at)'))
                    ->where('orders.payment', '=', 'paid')
                    ->where(DB::raw('YEAR(orders.created_at)'), '=', $year);
            })
            ->groupBy('months.month')
            ->get();
    }

    public function revenue7Day()
    {
        return $this->model
            ->select(DB::raw('
            dates.date,
            COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(orders.cart, "$.cartTotal"))), 0) as daily_revenue
        '))
            ->from(DB::raw('(
            SELECT CURDATE() - INTERVAL (a.a + (10*b.a) + (100 * c.a)) DAY as date
            FROM (
                SELECT 0 AS a UNION ALL
                SELECT 1 UNION ALL
                SELECT 2 UNION ALL
                SELECT 3 UNION ALL
                SELECT 4 UNION ALL
                SELECT 5 UNION ALL
                SELECT 6 UNION ALL
                SELECT 7 UNION ALL
                SELECT 8 UNION ALL
                SELECT 9
            ) as a
            CROSS JOIN (
                SELECT 0 AS a UNION ALL 
                SELECT 1 UNION ALL 
                SELECT 2 UNION ALL 
                SELECT 3 UNION ALL 
                SELECT 4 UNION ALL 
                SELECT 5 UNION ALL 
                SELECT 6 UNION ALL 
                SELECT 7 UNION ALL 
                SELECT 8 UNION ALL 
                SELECT 9
            ) as b
            CROSS JOIN (
                SELECT 0 AS a UNION ALL 
                SELECT 1 UNION ALL 
                SELECT 2 UNION ALL 
                SELECT 3 UNION ALL 
                SELECT 4 UNION ALL 
                SELECT 5 UNION ALL 
                SELECT 6 UNION ALL 
                SELECT 7 UNION ALL 
                SELECT 8 UNION ALL 
                SELECT 9
            ) as c
        ) as dates'))

            ->leftJoin('orders', function ($join) {
                $join->on(DB::raw('DATE(orders.created_at)'), '=', DB::raw('dates.date'))
                    ->where('orders.payment', '=', 'paid');
            })
            ->where(DB::raw('dates.date'), '>=', DB::raw('CURDATE() - INTERVAL 6 DAY'))
            ->groupBy(DB::raw('dates.date'))
            ->orderBy(DB::raw('dates.date'), 'ASC')
            ->get();
    }

    public function revenueCurrentMonth($currentMonth, $currentYear)
    {
        return $this->model->select(
            DB::raw('DAY(created_at) as day'),
            DB::raw('COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(orders.cart, "$.cartTotal"))), 0) as daily_revenue')
        )
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('orders.payment', '=', 'paid')
            ->groupBy('day')
            ->orderBy('day')
            ->get()->toArray();
    }

    public function chartPolar()
    {
        $currentMonth = now()->format('Y-m');

        $orders = $this->model->select(
            DB::raw("SUM(CASE WHEN confirm = 'pending' THEN 1 ELSE 0 END) as pending"),
            DB::raw("SUM(CASE WHEN confirm = 'confirm' AND payment ='unpaid' AND delivery ='pending' THEN 1 ELSE 0 END) as confirmed"),
            DB::raw("SUM(CASE WHEN confirm = 'confirm' AND delivery = 'processing' THEN 1 ELSE 0 END) as shipping"),
            DB::raw("SUM(CASE WHEN confirm = 'confirm' AND payment = 'paid' AND delivery = 'success' THEN 1 ELSE 0 END) as delivered"),
            DB::raw("SUM(CASE WHEN confirm = 'cancle' THEN 1 ELSE 0 END) as cancelled")
        )
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->first();

        return [
            'labels' => ['Chờ Xác Nhận', 'Đã Xác Nhận', 'Đang Vận Chuyển', 'Đã Giao', 'Đã Hủy'],
            'values' => [
                $orders->pending,
                $orders->confirmed,
                $orders->shipping,
                $orders->delivered,
                $orders->cancelled
            ]
        ];
    }

    public function orderByCustomer($customer_id = 0, $condition = [])
    {
        $query = $this->model->select(
            [
                'orders.*',
                'provinces.name as province_name',
                'districts.name as district_name',
                'wards.name as ward_name',
            ]
        )
            ->where('orders.customer_id', $customer_id)
            ->leftJoin('provinces', 'orders.province_id', '=', 'provinces.code')
            ->leftJoin('districts', 'orders.district_id', '=', 'districts.code')
            ->leftJoin('wards', 'orders.ward_id', '=', 'wards.code')
            ->with('products');
        if (isset($condition['keyword']) && !empty($condition['keyword'])) {
            $query->where(function ($query) use ($condition) {
                $keyword = $condition['keyword'];
                $query->where('orders.code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('orders.fullname', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('orders.phone', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('orders.address', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query->paginate(20);
    }

    /*Filer Time */

    public function getReportTime($startDate, $endDate)
    {
        return $this->model->select(
            DB::raw("DATE(created_at) as order_date"),
            DB::raw("COUNT(DISTINCT customer_id) as count_customer "),
            DB::raw("COUNT(DISTINCT orders.id) as count_order"),
            DB::raw("SUM(order_product.price * order_product.qty) as sum_revenue"),
            DB::raw("(SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(promotion, '$.discount'))) FROM orders WHERE DATE(created_at) = order_date GROUP BY DATE(created_at)) as sum_discount"),
        )
            ->join('order_product', 'order_product.order_id', '=', 'orders.id')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('orders.payment', '=', 'paid')
            ->groupBy('order_date')
            ->get()->toArray();
    }


    public function getTopSellingProducts()
    {
        $data = DB::table('order_product as op')
            ->join('orders as o', 'op.order_id', '=', 'o.id')
            ->join('products as p', 'op.product_id', '=', 'p.id')
            ->select(
                'p.name as product_name',
                DB::raw('SUM(op.qty) as total_sold')
            )
            ->where('o.delivery', '=', 'success')
            ->groupBy('p.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->toArray();

        $charts = [
            'labels' => array_column($data, 'product_name'),
            'values' => array_map('intval', array_column($data, 'total_sold')),
        ];

        return $charts;
    }


    public function getProductReportTime($startDate, $endDate)
    {
        $result = $this->model->select(
            DB::raw("IFNULL(product_variants.sku, products.code) as sku"),
            DB::raw("products.name as product_name"),
            DB::raw("COUNT(DISTINCT orders.customer_id) as count_customer"),
            DB::raw("COUNT(orders.id) as count_order"),
            DB::raw("SUM(order_product.price * order_product.qty) as sum_revenue"),
            DB::raw("SUM(JSON_UNQUOTE(JSON_EXTRACT(orders.promotion, '$.discount'))) as sum_discount")
        )
            ->join('order_product', 'order_product.order_id', '=', 'orders.id')
            ->leftJoin('product_variants', 'product_variants.uuid', '=', 'order_product.uuid')
            ->leftJoin('products', 'products.id', '=', 'order_product.product_id')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->where('orders.payment', '=', 'paid')
            ->groupBy('order_product.product_id', 'order_product.uuid')
            ->get()
            ->toArray();
        return $result;
    }



    public function getCustomerReportTime($startDate, $endDate)
    {
        return $this->model->select(
            DB::raw("sources.name as source_name"),
            DB::raw("COUNT(DISTINCT orders.customer_id) as count_customer"),
            DB::raw("COUNT(orders.id) as count_order"),
            DB::raw("SUM(order_product.price * order_product.qty) as sum_revenue"),
            DB::raw("(SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(promotion, '$.discount')))) as sum_discount")
        )
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('order_product', 'order_product.order_id', '=', 'orders.id')
            ->leftJoin('sources', 'sources.id', '=', 'customers.source_id')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->where('orders.payment', '=', 'paid')
            ->groupBy('sources.id')
            ->get()->toArray();
    }

    public function getTotalRevenueReportTime($startDate, $endDate)
    {
        return $this->model->select(
            DB::raw("SUM(order_product.price * order_product.qty) as sum_revenue"),
            DB::raw("(SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(promotion, '$.discount')))) as sum_discount")
        )
            ->join('order_product', 'order_product.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->where('orders.payment', '=', 'paid')
            ->get()
            ->toArray();
    }

    public function newOrder($startDate, $endDate)
    {
        return $this->model
            ->select(
                'orders.*',
                'provinces.name as province_name',
                'districts.name as district_name',
                'wards.name as ward_name'
            )
            ->leftJoin('provinces', 'provinces.code', '=', 'orders.province_id')
            ->leftJoin('districts', 'districts.code', '=', 'orders.district_id')
            ->leftJoin('wards', 'wards.code', '=', 'orders.ward_id')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->get();
    }

    public function chartRevenueAndCost()
    {
        $now = Carbon::now();
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $months[] = $now->copy()->subMonthsNoOverflow($i)->format('m/Y');
        }

        // Doanh thu theo tháng
        $revenue = $this->model
            ->selectRaw("DATE_FORMAT(updated_at, '%m/%Y') as month, SUM(JSON_UNQUOTE(JSON_EXTRACT(cart, '$.cartTotal'))) as total")
            ->where('delivery', 'success')
            ->whereBetween('updated_at', [Carbon::now()->subMonths(5)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('month')
            ->pluck('total', 'month');

        // Chi phí nhập hàng theo tháng
        $cost = DB::table('purchase_orders')
            ->selectRaw("DATE_FORMAT(updated_at, '%m/%Y') as month, SUM(total) as total")
            ->where('status', 'approved')
            ->whereBetween('updated_at', [Carbon::now()->subMonths(5)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('month')
            ->pluck('total', 'month');

        // Số lượng đơn hàng thành công theo tháng
        $orderCount = $this->model
            ->selectRaw("DATE_FORMAT(updated_at, '%m/%Y') as month, COUNT(*) as count")
            ->where('delivery', 'success')
            ->whereBetween('updated_at', [Carbon::now()->subMonths(5)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('month')
            ->pluck('count', 'month');

        // Lợi nhuận (Doanh thu - Chi phí)
        $profit = [];
        foreach ($months as $month) {
            $profit[$month] = (isset($revenue[$month]) ? (float) $revenue[$month] : 0) -
                (isset($cost[$month]) ? (float) $cost[$month] : 0);
        }

        // Chuẩn hóa dữ liệu cho biểu đồ
        $chartData = [
            'months'     => $months,
            'revenue'    => array_map(fn($m) => isset($revenue[$m]) ? (float) $revenue[$m] : 0, $months),
            'cost'       => array_map(fn($m) => isset($cost[$m]) ? (float) $cost[$m] : 0, $months),
            'orderCount' => array_map(fn($m) => isset($orderCount[$m]) ? (int) $orderCount[$m] : 0, $months),
            'profit'     => array_map(fn($m) => isset($profit[$m]) ? (float) $profit[$m] : 0, $months),
        ];

        Log::info('Chart Data:', $chartData);
        return $chartData;
    }
}
