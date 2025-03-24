<?php

namespace App\Repositories;

use App\Models\Promotion;
use App\Repositories\Interfaces\PromotionRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    protected $model;

    public function __construct(
        Promotion $model
    ) {
        $this->model = $model;
    }


    public function findByProduct(array $productId = [])
    {
        // Subquery lấy danh sách khuyến mãi
        $subquery = DB::table('promotions')
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('products', 'products.id', '=', 'ppv.product_id')
            ->select(
                'products.id as product_id',
                'products.price as product_price',
                'promotions.id as promotion_id',
                'promotions.discountValue',
                'promotions.discountType',
                'promotions.maxDiscountValue',
                DB::raw("
                LEAST(
                    CASE 
                        WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                        WHEN promotions.discountType = 'percent' THEN (products.price * promotions.discountValue / 100)
                        ELSE 0
                    END,
                    promotions.maxDiscountValue
                ) AS discount
            ")
            )
            ->where('products.publish', 2)
            ->where('promotions.publish', 2)
            ->whereIn('ppv.product_id', $productId)
            ->whereDate('promotions.endDate', '>', now())
            ->whereDate('promotions.startDate', '<=', now());

        // Lấy SQL của subquery và bindings
        $sqlSubquery = $subquery->toSql();
        $bindings = $subquery->getBindings();

        // Truy vấn chính, lọc ra khuyến mãi có giá trị giảm giá cao nhất
        return DB::table(DB::raw("({$sqlSubquery}) as promo"))
            ->mergeBindings($subquery)
            ->select(
                'promo.product_id',
                'promo.product_price',
                'promo.promotion_id',
                'promo.discountValue',
                'promo.discountType',
                'promo.maxDiscountValue',
                'promo.discount'
            )
            ->whereRaw("
            promo.discount = (
                SELECT MAX(p2.discount)
                FROM ({$sqlSubquery}) as p2
                WHERE p2.product_id = promo.product_id
            )
        ", $bindings)
            ->get();
    }


    public function findPromotionByVariantUuid($uuid = '')
    {
        // Subquery lấy danh sách khuyến mãi cho biến thể sản phẩm
        $subquery = DB::table('promotions')
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('product_variants as pv', 'pv.uuid', '=', 'ppv.variant_uuid')
            ->select(
                'pv.uuid as variant_uuid',
                'pv.price as variant_price',
                'promotions.id as promotion_id',
                'promotions.discountValue',
                'promotions.discountType',
                'promotions.maxDiscountValue',
                DB::raw("
                LEAST(
                    CASE 
                        WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                        WHEN promotions.discountType = 'percent' THEN (pv.price * promotions.discountValue / 100)
                        ELSE 0
                    END,
                    promotions.maxDiscountValue
                ) AS discount
            ")
            )
            ->where('promotions.publish', 2)
            ->where('pv.uuid', $uuid)
            ->whereDate('promotions.endDate', '>', now())
            ->whereDate('promotions.startDate', '<=', now());

        // Lấy SQL của subquery và bindings
        $sqlSubquery = $subquery->toSql();
        $bindings = $subquery->getBindings();

        // Truy vấn chính, lọc ra khuyến mãi có giá trị giảm giá cao nhất
        return DB::table(DB::raw("({$sqlSubquery}) as promo"))
            ->mergeBindings($subquery)
            ->select(
                'promo.variant_uuid',
                'promo.variant_price',
                'promo.promotion_id',
                'promo.discountValue',
                'promo.discountType',
                'promo.maxDiscountValue',
                'promo.discount'
            )
            ->whereRaw("
            promo.discount = (
                SELECT MAX(p2.discount)
                FROM ({$sqlSubquery}) as p2
                WHERE p2.variant_uuid = promo.variant_uuid
            )
        ", $bindings)
            ->first();
    }


    public function getPromotionByCartTotal()
    {
        return $this->model
            ->where('promotions.publish', 2)
            ->where('promotions.method', 'order_amount_range')
            ->where(function ($query) {
                $query->whereNull('promotions.endDate')
                    ->orWhere('promotions.endDate', '>=', now());
            })
            ->where('promotions.startDate', '<=', now())
            ->get();
    }
}
