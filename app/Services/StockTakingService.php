<?php

namespace App\Services;

use App\Services\Interfaces\StockTakingServiceInterface;
use App\Repositories\Interfaces\StockTakingRepositoryInterface as StockTakingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * Class CustomerService
 * @package App\Services
 */
class StockTakingService extends BaseService implements StockTakingServiceInterface
{
    protected $stockTakingRepository;

    public function __construct(
        StockTakingRepository $stockTakingRepository,
    ) {
        $this->stockTakingRepository = $stockTakingRepository;
        $this->controllerName = 'StockController';
    }

    // public function paginate($request)
    // {
    //     $condition = [
    //         'keyword' => $request->input('keyword') ? addslashes($request->input('keyword')) : null,
    //         'publish' => $request->integer('publish'),
    //         'created_at' => $request->input('created_at'),
    //         'dropdown' => [],
    //     ];

    //     $type = $request->string('type');
    //     if ($type == "import" || $type == "export" || $type == "return") {
    //         $condition['type'] = $type;
    //     }


    //     $cart = __('cart');
    //     if (is_array($cart)) {
    //         foreach ($cart as $key => $val) {
    //             $condition['dropdown'][$key] = $request->string($key);
    //         }
    //     }

    //     // Các tham số phân trang và sắp xếp
    //     $perPage = $request->integer('perpage', 20);
    //     $sortBy = $request->input('sort_by', 'created_at');
    //     $sortOrder = $request->input('sort_order', 'desc');

    //     // Các điều kiện truyền vào cho câu truy vấn
    //     $queryConditions = [
    //         ['stock_movements.*', 'products.name as product_name'],
    //         $condition,
    //         $perPage,
    //         ['path' => 'stock/stock-taking/index'],
    //         [$sortBy, $sortOrder],
    //         [['products', 'products.id', '=', 'stock_movements.product_id']],
    //         [],
    //         []
    //     ];

    //     // Tiến hành phân trang và trả kết quả
    //     $query = $this->stockTakingRepository->pagination(...$queryConditions);
    //     return $query;
    // }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only(['code', 'description', 'publish', 'products']);
            $payload['user_id'] = Auth::id();

            Log::info('StockTaking create payload:', $payload);

            $record = $this->stockTakingRepository->create($payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            // Ghi log lỗi
            Log::error('StockTaking create failed: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
}
