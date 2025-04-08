<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Traits\QueryScopes;

class Post extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'image',
        'publish',
        'order',
        'user_id',
        'post_catalogue_id',
        'name',
        'description',
        'content',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'canonical'
    ];

    protected $table = 'posts';

    public function post_catalogues()
    {
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_post', 'post_id', 'post_catalogue_id');
    }

    public static function hasPosts($catalogueId)
    {
        $hasDirectPosts = self::where('post_catalogue_id', $catalogueId)->exists();

        $hasRelationPosts = DB::table('post_catalogue_post')
            ->where('post_catalogue_id', $catalogueId)
            ->exists();

        return $hasDirectPosts || $hasRelationPosts;
    }
}
