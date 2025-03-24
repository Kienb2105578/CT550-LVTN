<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Cart extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'customer_id',
        'guest_cookie',
    ];

    protected $table = 'carts';

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_product', 'cart_id', 'product_id')
            ->withPivot('uuid', 'variant_id', 'name', 'qty', 'price', 'option')
            ->withTimestamps();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
