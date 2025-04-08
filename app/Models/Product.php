<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Traits\QueryScopes;

class Product extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'image',
        'album',
        'publish',
        'features',
        'order',
        'user_id',
        'product_catalogue_id',
        'quantity',
        'price',
        'made_in',
        'code',
        'attributeCatalogue',
        'attribute',
        'variant',
        'qrcode',
        'name',
        'description',
        'content',
        'canonical'
    ];

    protected $casts = [
        'attribute' => 'json'
    ];

    protected $table = 'products';

    public function product_catalogues()
    {
        return $this->belongsToMany(ProductCatalogue::class, 'product_catalogue_product', 'product_id', 'product_catalogue_id');
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product_variant', 'product_id', 'promotion_id')
            ->withPivot(
                'variant_uuid',
                'model',
            )->withTimestamps();
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_product', 'product_id', 'cart_id')
            ->withPivot('uuid', 'name', 'qty', 'price', 'option')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id')
            ->withPivot(
                'uuid',
                'name',
                'qty',
                'price',
                'priceOriginal',
                'option',
            );
    }

    public function review_morph()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function purchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'product_id', 'id');
    }

    public static function hasProducts($catalogueId)
    {
        $hasDirectProducts = self::where('product_catalogue_id', $catalogueId)->exists();

        $hasRelationProducts = DB::table('product_catalogue_product')
            ->where('product_catalogue_id', $catalogueId)
            ->exists();

        return $hasDirectProducts || $hasRelationProducts;
    }
}
