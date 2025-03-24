<?php

namespace App\Services;

use App\Services\Interfaces\StockMovementServiceInterface;
use App\Repositories\Interfaces\StockMovementRepositoryInterface as StockMovementRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * Class CustomerService
 * @package App\Services
 */
class StockMovementService extends BaseService implements StockMovementServiceInterface
{
    protected $stockMovementRepository;

    public function __construct(
        StockMovementRepository $stockMovementRepository,
    ) {
        $this->stockMovementRepository = $stockMovementRepository;
        $this->controllerName = 'StockController';
    }

    public function paginate($request)
    {
        $condition = [
            'keyword' => $request->input('keyword') ? addslashes($request->input('keyword')) : null,
            'publish' => $request->integer('publish'),
            'created_at' => $request->input('created_at'),
            'dropdown' => [],
        ];

        $type = $request->string('type');
        if ($type == "import" || $type == "export" || $type == "return") {
            $condition['type'] = $type;
        }


        $cart = __('cart');
        if (is_array($cart)) {
            foreach ($cart as $key => $val) {
                $condition['dropdown'][$key] = $request->string($key);
            }
        }

        // Các tham số phân trang và sắp xếp
        $perPage = $request->integer('perpage', 20);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Các điều kiện truyền vào cho câu truy vấn
        $queryConditions = [
            ['stock_movements.*', 'products.name as product_name'],
            $condition,
            $perPage,
            ['path' => 'stock/stock-taking/index'],
            [$sortBy, $sortOrder],
            [['products', 'products.id', '=', 'stock_movements.product_id']],
            [],
            []
        ];

        // Tiến hành phân trang và trả kết quả
        $query = $this->stockMovementRepository->pagination(...$queryConditions);
        return $query;
    }
}
