<?php

namespace App\Repositories;

use App\Models\StockMovement;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class StockMovementRepository extends BaseRepository implements StockMovementRepositoryInterface
{
    protected $model;

    public function __construct(
        StockMovement $model
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
                    $subQuery->whereHas('product', function ($productQuery) use ($condition) {
                        $productQuery->where('name', 'LIKE', '%' . $condition['keyword'] . '%');
                    });
                });
            })
            ->when(isset($condition['type']) && $condition['type'] !== null, function ($q) use ($condition) {
                $q->where('type', $condition['type']);
            })
            ->publish($condition['publish'] ?? null)
            ->customDropdownFilter($condition['dropdown'] ?? null)
            ->relationCount($relations ?? null)
            ->CustomWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->customJoin([
                ['products', 'products.id', '=', 'stock_movements.product_id'],
            ])
            ->customGroupBy($extend['groupBy'] ?? null)
            ->customerCreatedAt($condition['created_at'] ?? null)
            ->orderBy($orderBy[0] ?? 'created_at', $orderBy[1] ?? 'DESC')
            ->paginate($perPage)
            ->withQueryString()->withPath(env('APP_URL') . ($extend['path'] ?? ''));
    }
}
