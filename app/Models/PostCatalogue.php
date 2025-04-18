<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\QueryScopes;

class PostCatalogue extends Model
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
        'name',
        'canonical',
        'description',
        'content',
    ];

    protected $table = 'post_catalogues';




    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_catalogue_post', 'post_catalogue_id', 'post_id');
    }




    public static function isNodeCheck($id)
    {
        $postCatalogue = PostCatalogue::find($id);

        if (!$postCatalogue) return false;
        return ($postCatalogue->rgt - $postCatalogue->lft > 1);
    }
}
