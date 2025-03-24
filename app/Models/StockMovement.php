<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

class StockMovement extends Model
{
    use HasFactory, QueryScopes;
    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id',
        'variant_id',
        'batch_id',
        'purchase_order_id',
        'type',
        'quantity',
        'reference_id',
        'reference_type',
        'user_id',
        'created_at',
    ];

    public $timestamps = false;

    /**
     * Quan hệ với Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Quan hệ với ProductVariant (nếu có)
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Quan hệ với InventoryBatch
     */
    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }

    /**
     * Quan hệ với PurchaseOrder
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Quan hệ với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
