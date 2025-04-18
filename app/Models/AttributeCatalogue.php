<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\QueryScopes;

class AttributeCatalogue extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'image',
        'publish',
        'user_id',
        'name',
        'description',
        'canonical'
    ];

    protected $table = 'attribute_catalogues';

    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'attribute_catalogue_id', 'id');
    }


    public static function isNodeCheck($id = 0)
    {
        $attributeCatalogue = AttributeCatalogue::find($id);

        if ($attributeCatalogue->rgt - $attributeCatalogue->lft !== 1) {
            return false;
        }

        return true;
    }
}
