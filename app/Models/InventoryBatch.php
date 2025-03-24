<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class InventoryBatch extends Model
{
    use HasFactory, QueryScopes;

    protected $table = 'inventory_batches';

    protected $fillable = [
        'product_id',
        'variant_id',
        'purchase_order_id',
        'initial_quantity',
        'quantity',
        'price',
        'publish',
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Quan hệ với sản phẩm
     */
    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Quan hệ với biến thể sản phẩm
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Quan hệ với đơn đặt hàng nhập hàng
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
