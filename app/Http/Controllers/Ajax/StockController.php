<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\InventoryBatchRepositoryInterface as InventoryBatchRepository;
use App\Services\Interfaces\InventoryBatchServiceInterface  as InventoryBatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{

    protected $inventoryBatchRepository;
    protected $inventoryBatchService;

    public function __construct(
        InventoryBatchRepository $inventoryBatchRepository,
        InventoryBatchService $inventoryBatchService,
    ) {
        $this->inventoryBatchRepository = $inventoryBatchRepository;
        $this->inventoryBatchService = $inventoryBatchService;
        parent::__construct();
    }

    public function getInventoryWithPurchase(Request $request)
    {
        $purchaseOrderId = $request->_id;
        Log::info('Received purchaseOrderId:', ['purchaseOrderId' => $purchaseOrderId]);
        $inventory = $this->inventoryBatchRepository->getInventoryWithPurchase($purchaseOrderId);
        return response()->json($inventory);
    }

    public function getInventoryWithProduct(Request $request)
    {
        $productId = $request->_id; // Lấy product_id từ request
        $variantId = $request->variant_id; // Lấy variant_id từ request (có thể là null
        $inventory = $this->inventoryBatchRepository->getInventoryDetails($productId, $variantId);
        log::info("ÌNO", ['$inventory ' => $inventory]);
        return response()->json($inventory);
    }

    public function getInventoryWithTime(Request $request)
    {
        $product_id = $request->input('product_id');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $data = $this->inventoryBatchRepository->getInventoryWithTime($product_id, $startDate, $endDate);

        return response()->json($data);
    }

    public function getReport(Request $request)
    {
        $catalogue_id = $request->input('catalogue_id');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $products = $this->inventoryBatchRepository->getReport($catalogue_id, $startDate, $endDate);

        return response()->json([
            'products' => $products
        ]);
    }


    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $updated = $this->inventoryBatchService->updatePublish($id, $request);

        if ($updated) {
            return response()->json([
                'flag' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
        }
        return response()->json([
            'flag' => false,
            'message' => 'Cập nhật thất bại'
        ]);
    }
}
