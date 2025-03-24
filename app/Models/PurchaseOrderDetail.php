<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

class PurchaseOrderDetail extends Model
{
    use HasFactory, QueryScopes;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'uuid',
        'quantity',
        'price',
    ];

    protected $table = 'purchase_order_details';

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
