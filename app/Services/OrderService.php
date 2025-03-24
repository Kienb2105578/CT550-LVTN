<?php

namespace App\Services;

use App\Services\Interfaces\OrderServiceInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
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
class OrderService extends BaseService implements OrderServiceInterface
{
    protected $orderRepository;
    protected $productVariantRepository;
    protected $productRepository;


    public function __construct(
        OrderRepository $orderRepository,
        ProductVariantRepository $productVariantRepository,
        ProductRepository $productRepository,
    ) {
        $this->orderRepository = $orderRepository;
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
        $orders = $this->orderRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'order/index'],
            ['id', 'desc'],
        );

        return $orders;
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $payload = $request->input('payload');

            Log::info('Payload received for update:', ['id' => $id, 'payload' => $payload]);

            if (isset($payload['confirm']) && $payload['confirm'] === 'returned') {
                $order = $this->orderRepository->getOrderByOrderId($id);
                foreach ($order['products'] as $item) {
                    $productId = $item['product_id'];
                    $variantId = $item['variant_id'] ?? null;
                    $quantityToReturn = $item['qty'];
                    $batchId = $item['batch_id'] ?? null;

                    Log::info("Chi tiết sản phẩm hoàn hàng:", [$item]);

                    if ($batchId) {
                        $batch = DB::table('inventory_batches')->where('id', $batchId)->first();
                        if ($batch) {
                            DB::table('inventory_batches')
                                ->where('id', $batchId)
                                ->update(['quantity' => $batch->quantity + $quantityToReturn]);

                            Log::info("Cộng {$quantityToReturn} vào batch {$batchId} của sản phẩm ID: {$productId}, Biến thể: {$variantId}.");

                            DB::table('stock_movements')->insert([
                                'product_id'       => $productId,
                                'variant_id'       => $variantId,
                                'batch_id'         => $batchId,
                                'user_id'           =>  Auth::id(),
                                'purchase_order_id' => $batch->purchase_order_id,
                                'type'             => 'return',
                                'quantity'         => $quantityToReturn,
                                'reference_id'     => $id,
                                'reference_type'   => 'return_order',
                                'created_at'       => now(),
                            ]);

                            continue;
                        }
                    }

                    $batches = DB::table('inventory_batches')
                        ->where('product_id', $productId)
                        ->when($variantId, function ($query) use ($variantId) {
                            return $query->where('variant_id', $variantId);
                        })
                        ->whereColumn('quantity', '<', 'initial_quantity')
                        ->orderBy('created_at', 'asc')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($quantityToReturn <= 0) {
                            break;
                        }

                        $spaceLeft = $batch->initial_quantity - $batch->quantity;
                        $addQuantity = min($spaceLeft, $quantityToReturn);

                        DB::table('inventory_batches')
                            ->where('id', $batch->id)
                            ->update(['quantity' => $batch->quantity + $addQuantity]);

                        Log::info("Đã hoàn lại {$addQuantity} sản phẩm (ID: {$productId}, Biến thể: {$variantId}) vào batch {$batch->id}.");

                        DB::table('stock_movements')->insert([
                            'product_id'       => $productId,
                            'variant_id'       => $variantId,
                            'batch_id'         => $batch->id,
                            'user_id'          =>  Auth::id(),
                            'purchase_order_id' => $batch->purchase_order_id,
                            'type'             => 'return',
                            'quantity'         => $addQuantity,
                            'reference_id'     => $id,
                            'reference_type'   => 'return_order',
                            'created_at'       => now(),
                        ]);

                        $quantityToReturn -= $addQuantity;
                    }

                    if ($quantityToReturn > 0) {
                        $newBatchId = DB::table('inventory_batches')->insertGetId([
                            'product_id'        => $productId,
                            'variant_id'        => $variantId,
                            'purchase_order_id' => null,
                            'initial_quantity'  => $quantityToReturn,
                            'quantity'          => $quantityToReturn,
                            'created_at'        => now(),
                            'updated_at'        => now(),
                        ]);

                        DB::table('stock_movements')->insert([
                            'product_id'       => $productId,
                            'variant_id'       => $variantId,
                            'batch_id'         => $newBatchId,
                            'purchase_order_id' => null,
                            'type'             => 'return',
                            'quantity'         => $quantityToReturn,
                            'reference_id'     => $id,
                            'reference_type'   => 'return_order',
                            'created_at'       => now(),
                        ]);
                    }
                }
            }
            // if (isset($payload['delivery']) && $payload['delivery'] === 'processing')
            if (isset($payload['confirm']) && $payload['confirm'] === 'confirm') {
                $order = $this->orderRepository->getOrderByOrderId($id);
                foreach ($order['products'] as $item) {
                    $productId = $item['product_id'];
                    $variantId = $item['variant_id'] ?? null;
                    $quantity = $item['qty'];
                    $batchId = $item['batch_id'] ?? null;

                    Log::info("Chi tiết sản phẩm đang giao hàng:", [$item]);

                    DB::table('stock_movements')->insert([
                        'product_id'       => $productId,
                        'variant_id'       => $variantId,
                        'batch_id'         => $batchId,
                        'user_id'          => Auth::id(),
                        'purchase_order_id' => $batchId ? DB::table('inventory_batches')->where('id', $batchId)->value('purchase_order_id') : null,
                        'type'             => 'export',
                        'quantity'         => $quantity,
                        'reference_id'     => $id,
                        'reference_type'   => 'sale_order',
                        'created_at'       => now(),
                    ]);

                    Log::info("Ghi nhận trạng thái giao hàng cho sản phẩm ID: {$productId}, Biến thể: {$variantId}, Số lượng: {$quantity}, Batch: {$batchId}");
                }
            }
            //Lúc nào cũng có
            $this->orderRepository->update($id, $payload);


            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi khi cập nhật đơn hàng {$id}: " . $e->getMessage());
            return false;
        }
    }

    public function updateCancel($id)
    {
        DB::beginTransaction();
        try {
            $order = $this->orderRepository->getOrderByOrderId($id);
            foreach ($order['products'] as $item) {
                $productId = $item['product_id'];
                $variantId = $item['variant_id'] ?? null;
                $quantityToReturn = $item['qty'];
                $batchId = $item['batch_id'] ?? null; // Lấy batch_id nếu có

                Log::info("Chi tiết sản phẩm:", [$item]);

                if ($batchId) {
                    // Nếu có batch_id, cập nhật trực tiếp vào batch đó
                    $batch = DB::table('inventory_batches')->where('id', $batchId)->first();

                    if ($batch) {
                        DB::table('inventory_batches')
                            ->where('id', $batchId)
                            ->update(['quantity' => $batch->quantity + $quantityToReturn]);

                        Log::info("Cộng {$quantityToReturn} vào batch {$batchId} của sản phẩm ID: {$productId}, Biến thể: {$variantId}.");
                        continue; // Đã xử lý xong batch_id này, chuyển sang sản phẩm tiếp theo
                    }
                }

                // Nếu không có batch_id hoặc batch không tồn tại, thực hiện logic cũ
                $batches = DB::table('inventory_batches')
                    ->where('product_id', $productId)
                    ->when($variantId, function ($query) use ($variantId) {
                        return $query->where('variant_id', $variantId);
                    })
                    ->whereColumn('quantity', '<', 'initial_quantity')
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($batches as $batch) {
                    if ($quantityToReturn <= 0) {
                        break;
                    }

                    $spaceLeft = $batch->initial_quantity - $batch->quantity;
                    $addQuantity = min($spaceLeft, $quantityToReturn);

                    DB::table('inventory_batches')
                        ->where('id', $batch->id)
                        ->update(['quantity' => $batch->quantity + $addQuantity]);


                    $quantityToReturn -= $addQuantity;
                }

                if ($quantityToReturn > 0) {
                    $newBatchId = DB::table('inventory_batches')->insertGetId([
                        'product_id'        => $productId,
                        'variant_id'        => $variantId,
                        'purchase_order_id' => null,
                        'initial_quantity'  => $quantityToReturn,
                        'quantity'          => $quantityToReturn,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }
            }

            $this->orderRepository->update($id, ['confirm' => 'cancle']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi khi huỷ đơn hàng {$id}: " . $e->getMessage());
            return false;
        }
    }



    public function updateReturn($id)
    {
        DB::beginTransaction();
        try {
            $this->orderRepository->update($id, [
                'confirm'   => 'confirm',
                'payment'   => 'refunded',
                'delivery'  => 'returned',
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi khi cập nhật đơn hàng {$id}: " . $e->getMessage());
            return false;
        }
    }

    public function updatePaymentOnline($payload, $order)
    {
        DB::beginTransaction();
        try {
            $this->orderRepository->update($order->id, $payload);
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


    public function getOrderItemImage($order)
    {
        foreach ($order->products as $key => $val) {
            $uuid = $val->pivot->uuid;
            if (!is_null($uuid)) {
                $variant = $this->productVariantRepository->findByCondition([
                    ['uuid', '=', $uuid]
                ]);

                if ($variant) {
                    $variantImage = explode(',', $variant->album)[0] ?? null;
                } else {
                    $variantImage = null; // Hoặc gán ảnh mặc định
                }

                $val->image = $variantImage;
            }
        }

        return $order;
    }


    public function statistic()
    {
        $month = now()->month;
        $year  = now()->year;
        $previousMonth = ($month == 1) ? 12 : $month - 1;
        $previousYear = ($month == 1) ? $year - 1 : $year;


        $orderCurrentMonth = $this->orderRepository->getOrderByTime($month, $year);
        $orderPreviousMonth = $this->orderRepository->getOrderByTime($previousMonth, $previousYear);

        return [
            'orderCurrentMonth' => $orderCurrentMonth,
            'orderPreviousMonth' => $orderPreviousMonth,
            'grow' => growth($orderCurrentMonth, $orderPreviousMonth),
            'totalOrders' => $this->orderRepository->getTotalOrders(),
            'cancleOrders' => $this->orderRepository->getCancleOrders(),
            'revenue' => $this->orderRepository->revenueOrders(),
            'revenueChart' => convertRevenueChartData($this->orderRepository->revenueByYear($year)),
        ];
    }

    public function ajaxOrderChart($request)
    {
        $type = $request->input('chartType');
        switch ($type) {
            case 1:
                $year  = now()->year;
                $response = convertRevenueChartData($this->orderRepository->revenueByYear($year));
                break;
            case 7:
                $response = convertRevenueChartData($this->orderRepository->revenue7Day(), 'daily_revenue', 'date', 'Ngày');
                break;
            case 30:

                $currentMonth = now()->month;
                $currentYear  = now()->year;
                $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;

                $allDays = range(1, $daysInMonth);
                $temp = $this->orderRepository->revenueCurrentMonth($currentMonth, $currentYear);
                $label = [];
                $data = [];
                $temp2 = array_map(function ($day) use ($temp, &$label, &$data) {
                    $found = collect($temp)->first(function ($record) use ($day) {
                        return $record['day'] == $day;
                    });
                    $label[] = 'Ngày ' . $day;
                    $data[] = $found ? $found['daily_revenue'] : 0;
                }, $allDays);
                $response = [
                    'label' => $label,
                    'data' => $data,
                ];
                break;
        }

        return $response;
    }



    private function paginateSelect()
    {
        return [
            'id',
            'code',
            'fullname',
            'phone',
            'email',
            'province_id',
            'district_id',
            'ward_id',
            'address',
            'description',
            'promotion',
            'cart',
            'customer_id',
            'guest_cookie',
            'method',
            'confirm',
            'payment',
            'delivery',
            'shipping',
            'created_at',
        ];
    }
}
