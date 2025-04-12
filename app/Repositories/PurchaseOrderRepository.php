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
        array $purchaseOrderBy = ['created_at', 'DESC'],
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
            ->orderBy($purchaseOrderBy[0], $purchaseOrderBy[1])
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
                    ->groupBy('purchase_order_details.product_id');
            }])
            ->find($id);
    }
}
