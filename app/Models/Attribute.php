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
        'publish',
        'user_id',
        'attribute_catalogue_id',
        'name',
        'canonical',
        'description',
    ];

    protected $table = 'attributes';


    public function attribute_catalogue()
    {
        return $this->belongsTo(AttributeCatalogue::class, 'attribute_catalogue_id', 'id');
    }

    public function product_variants()
    {
        return  $this->belongsToMany(ProductVariant::class, 'product_variant_attribute', 'attribute_id', 'attribute_id')
            ->withPivot(
                'name',
            )->withTimestamps();
    }
}
