<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\QueryScopes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'image',
        'album',
        'publish',
        'follow',
        'order',
        'user_id',
        'attribute_catalogue_id',
        'name',
        'canonical',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'description',
        'content'
    ];

    protected $table = 'attributes';


    public function attribute_catalogues()
    {
        return $this->belongsToMany(AttributeCatalogue::class, 'attribute_catalogue_attribute', 'attribute_id', 'attribute_catalogue_id');
    }

    public function product_variants()
    {
        return  $this->belongsToMany(ProductVariant::class, 'product_variant_attribute', 'attribute_id', 'attribute_id')
            ->withPivot(
                'name',
            )->withTimestamps();
    }
}
