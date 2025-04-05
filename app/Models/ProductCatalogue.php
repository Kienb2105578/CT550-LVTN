<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\QueryScopes;

class ProductCatalogue extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'image',
        'publish',
        'order',
        'user_id',
        'attribute',
        'name',
        'description',
        'canonical'
    ];

    protected $table = 'product_catalogues';

    protected $attributes = [
        'publish' => 2,
    ];

    protected $casts = [
        'attribute' => 'json',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_catalogue_product', 'product_catalogue_id', 'product_id');
    }


    public static function isNodeCheck($id = 0)
    {
        $productCatalogue = ProductCatalogue::find($id);

        if ($productCatalogue->rgt - $productCatalogue->lft !== 1) {
            return false;
        }

        return true;
    }
}
