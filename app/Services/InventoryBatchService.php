<?php

namespace App\Services;

use App\Services\Interfaces\InventoryBatchServiceInterface;
use App\Repositories\Interfaces\InventoryBatchRepositoryInterface as InventoryBatchRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * Class CustomerService
 * @package App\Services
 */
class InventoryBatchService extends BaseService implements InventoryBatchServiceInterface
{
    protected $inventoryBatchRepository;

    public function __construct(
        InventoryBatchRepository $inventoryBatchRepository,
    ) {
        $this->inventoryBatchRepository = $inventoryBatchRepository;
        $this->controllerName = 'StockController';
    }



    public function paginate($request)
    {
        $condition = [
            'keyword' => $request->input('keyword') ? addslashes($request->input('keyword')) : null,
            'publish' => $request->integer('publish'),
            'created_at' => $request->input('created_at'),
            'dropdown' => []
        ];

        foreach (__('cart') as $key => $val) {
            $condition['dropdown'][$key] = $request->string($key);
        }

        $perPage = $request->integer('perpage', 10);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        return $this->inventoryBatchRepository->pagination(
            ['inventory_batches.*', 'products.name as product_name', 'suppliers.name as supplier_name'],
            $condition,
            $perPage,
            ['path' => 'stock/inventory/index'],
            [$sortBy, $sortOrder],
            [
                ['products', 'products.id', '=', 'inventory_batches.product_id'],
                ['purchase_orders', 'purchase_orders.id', '=', 'inventory_batches.purchase_order_id'],
                ['suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id'],
            ]
        );
    }

    public function updatePublish($id, $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only($this->payload());
            $payload['publish'] = $request->status == 2 ? 2 : 1;

            // Ghi log để kiểm tra giá trị
            Log::info("Updating inventory batch ID: {$id}", $payload);

            $this->inventoryBatchRepository->update($id, $payload);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating inventory batch: " . $e->getMessage());
            return false;
        }
    }


    protected function payload()
    {
        return [
            'purchase_order_id',
            'product_id',
            'variant_id',
            'initial_quantity',
            'quantity',
            'price',
            'publish'
        ];
    }
}
