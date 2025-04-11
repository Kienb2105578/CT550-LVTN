<?php

namespace App\Services;

use App\Services\Interfaces\PurchaseOrderServiceInterface;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface as PurchaseOrderRepository;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Carbon;

/**
 * Class CustomerService
 * @package App\Services
 */
class PurchaseOrderService extends BaseService implements PurchaseOrderServiceInterface
{
    protected $purchaseOrderRepository;
    protected $productVariantRepository;
    protected $productRepository;


    public function __construct(
        PurchaseOrderRepository $purchaseOrderRepository,
        ProductVariantRepository $productVariantRepository,
        ProductRepository $productRepository,
    ) {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productRepository = $productRepository;
    }



    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');

        foreach (__('cart') as $key => $val) {
            $condition['dropdown'][$key] = $request->string($key);
        }

        $condition['created_at'] = $request->input('created_at');
        $perPage = $request->integer('perpage');

        $purchaseOrders = $this->purchaseOrderRepository->pagination(
            [
                'purchase_orders.*',
                'suppliers.name as supplier_name',
                'users.name as user_name',
                'user_catalogues.name as user_catalogue_name'
            ],
            $condition,
            $perPage,
            ['path' => 'purchase-order/index'],
            ['created_at', 'desc'],
            [
                ['suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id', 'left'],
                ['users', 'users.id', '=', 'purchase_orders.user_id', 'left'],
                ['user_catalogues', 'user_catalogues.id', '=', 'users.user_catalogue_id', 'left'],
            ]
        );
        return $purchaseOrders;
    }


    public function create($request)
    {
        DB::beginTransaction();
        try {
            $perchaseorders = $this->createPurchaseOrder($request);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $this->updatePurchaseOrder($id, $request);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->purchaseOrderRepository->find($id);
            $purchaseOrder->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }


    private function createPurchaseOrder($request)
    {
        $payload = $request->only(['supplier_id', 'product_id', 'quantity', 'price', 'code']);
        $total = 0;

        foreach ($payload['price'] as $productId => &$priceData) {
            if (is_array($priceData)) {
                foreach ($priceData as $variantId => &$variantPrice) {
                    $variantPrice = (int) str_replace(['.', ','], '', $variantPrice);
                }
                unset($variantPrice);
            } else {
                $priceData = (int) str_replace(['.', ','], '', $priceData);
            }
        }
        unset($priceData);

        foreach ($payload['quantity'] as $productId => $quantityData) {
            if (is_scalar($quantityData)) {
                $quantity = (int) $quantityData;
                $price = $payload['price'][$productId] ?? 0;
                $total += $quantity * $price;
            } elseif (is_array($quantityData)) {
                foreach ($quantityData as $variantId => $quantity) {
                    $price = $payload['price'][$productId][$variantId] ?? 0;
                    $total += ((int) $quantity) * $price;
                }
            }
        }
        $payload['user_id'] = Auth::id();
        $payload['total'] = $total;
        $payload['status'] = 'pending';

        try {
            $purchaseOrder = $this->purchaseOrderRepository->create($payload);
            $this->createPurchaseOrderDetails($purchaseOrder->id, $payload);
            return $purchaseOrder;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể tạo đơn hàng'], 500);
        }
    }

    private function createPurchaseOrderDetails($purchaseOrderId, $payload)
    {
        foreach ($payload['quantity'] as $productId => $quantityData) {
            if (is_string($quantityData) || is_numeric($quantityData)) {
                $quantity = (int) $quantityData;
                $price = isset($payload['price'][$productId])
                    ? (float) str_replace(['.', ','], '', $payload['price'][$productId])
                    : 0;

                $subtotal = $quantity * $price;

                $data = [
                    'purchase_order_id' => $purchaseOrderId,
                    'product_id' => (int) $productId,
                    'variant_id' => null,
                    'quantity' => $quantity,
                    'price' => (int) $price,
                    'subtotal' => $subtotal,
                    'product_name' => $this->productRepository->getProductName($productId),
                    'uuid' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('purchase_order_details')->insert($data);
            }
            // Trường hợp sản phẩm có biến thể
            elseif (is_array($quantityData)) {
                foreach ($quantityData as $variantId => $quantity) {
                    $price = isset($payload['price'][$productId][$variantId])
                        ? (float) str_replace(['.', ','], '', $payload['price'][$productId][$variantId])
                        : 0;

                    $subtotal = ((int) $quantity) * $price;

                    $nameProduct = $this->productRepository->getProductName($productId);
                    $variant = $this->productVariantRepository->getVariantInfo($variantId);

                    $data = [
                        'purchase_order_id' => $purchaseOrderId,
                        'product_id' => (int) $productId,
                        'uuid' => $variant->uuid ?? null,
                        'variant_id' => $variant->id,
                        'quantity' => (int) $quantity,
                        'price' => (int)$price,
                        'subtotal' => $subtotal,
                        'product_name' => trim($nameProduct . ' - ' . ($variant->name ?? '')),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('purchase_order_details')->insert($data);
                }
            }
        }
    }


    private function updatePurchaseOrder($id, $request)
    {
        $payload = $request->only(['supplier_id', 'product_id', 'quantity', 'price', 'code', 'status']);
        $total = 0;

        foreach ($payload['price'] as $productId => &$priceData) {
            if (is_array($priceData)) {
                foreach ($priceData as $variantId => &$variantPrice) {
                    $variantPrice = (int) str_replace(['.', ','], '', $variantPrice);
                }
            } else {
                $priceData = (int) str_replace(['.', ','], '', $priceData);
            }
        }
        unset($priceData, $variantPrice);

        foreach ($payload['quantity'] as $productId => $quantityData) {
            if (is_string($quantityData) || is_numeric($quantityData)) {
                $quantity = (int) $quantityData;
                $price = $payload['price'][$productId] ?? 0;
                $total += $quantity * $price;
            } elseif (is_array($quantityData)) {
                foreach ($quantityData as $variantId => $quantity) {
                    $price = $payload['price'][$productId][$variantId] ?? 0;
                    $total += ((int) $quantity) * $price;
                }
            }
        }

        $payload['total'] = $total;
        $payload['user_id'] = Auth::id();

        try {
            $purchaseOrder = $this->purchaseOrderRepository->update($id, $payload);
            DB::table('purchase_order_details')->where('purchase_order_id', $id)->delete();
            $this->createPurchaseOrderDetails($id, $payload);


            if ($payload['status'] === 'approved') {
                foreach ($payload['quantity'] as $productId => $quantityData) {
                    if (is_string($quantityData) || is_numeric($quantityData)) {
                        $batchId = DB::table('inventory_batches')->insertGetId([
                            'product_id'        => $productId,
                            'user_id'           => Auth::id(),
                            'variant_id'        => null,
                            'purchase_order_id' => $id,
                            'price'             => $payload['price'][$productId] ?? 0,
                            'initial_quantity'  => (int) $quantityData,
                            'quantity'          => (int) $quantityData,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);
                        DB::table('stock_movements')->insert([
                            'product_id'     => $productId,
                            'variant_id'     => null,
                            'user_id'        => Auth::id(),
                            'batch_id'       => $batchId,
                            'purchase_order_id' => $id,
                            'type'           => 'import',
                            'quantity'       => (int) $quantityData,
                            'reference_id'   => $id,
                            'reference_type' => 'purchase_order',
                            'created_at'     => now()
                        ]);
                    } elseif (is_array($quantityData)) {
                        foreach ($quantityData as $variantId => $quantity) {
                            $batchId = DB::table('inventory_batches')->insertGetId([
                                'product_id'        => $productId,
                                'user_id'           => Auth::id(),
                                'variant_id'        => $variantId,
                                'purchase_order_id' => $id,
                                'initial_quantity'  => (int) $quantity,
                                'quantity'          => (int) $quantity,
                                'price'             => $payload['price'][$productId][$variantId] ?? 0,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ]);


                            DB::table('stock_movements')->insert([
                                'product_id'     => $productId,
                                'variant_id'     => $variantId,
                                'user_id'        => Auth::id(),
                                'batch_id'       => $batchId,
                                'purchase_order_id' => $id,
                                'type'           => 'import',
                                'quantity'       => (int) $quantity,
                                'reference_id'   => $id,
                                'reference_type' => 'purchase_order',
                                'created_at'     => now()
                            ]);
                        }
                    }
                }
            }
            return $purchaseOrder;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể cập nhật đơn hàng'], 500);
        }
    }
}
