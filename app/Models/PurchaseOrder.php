<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Thêm dòng này
use App\Traits\QueryScopes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, QueryScopes; // Thêm SoftDeletes vào

    protected $fillable = [
        'code',
        'supplier_id',
        'total',
        'status',
        'note',
        'user_id'
    ];

    protected $table = 'purchase_orders';
    protected $dates = ['deleted_at'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function purchase_order_details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
