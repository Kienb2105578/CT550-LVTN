<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'quantity',
        'sku',
        'price',
        'barcode',
        'file_name',
        'file_url',
        'album',
        'publish',
        'user_id',
        'uuid',
        'name',
    ];

    protected $table = 'product_variants';


    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function attributes()
    {
        return  $this->belongsToMany(Attribute::class, 'product_variant_attribute', 'product_variant_id', 'attribute_id');
    }
}
