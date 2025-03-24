<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    protected $model;

    public function __construct(
        Post $model
    ) {
        $this->model = $model;
    }

    public function getAllPosts()
    {
        return $this->model->select([
            'id',
            'post_catalogue_id',
            'image',
            'icon',
            'album',
            'publish',
            'follow',
            'video',
            'name',
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical',
            'updated_at'
        ])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
    }


    public function getPostById(int $id = 0)
    {
        return $this->model->select([
            'id',
            'post_catalogue_id',
            'image',
            'icon',
            'album',
            'publish',
            'follow',
            'video',
            'name',
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical',
        ])
            ->where('id', $id)
            ->first();
    }

    public function findByCondition(
        $condition = [],
        $flag = false,
        $relation = [],
        array $orderBy = ['id', 'desc'],
        array $param = [],
        array $withCount = [],
    ) {

        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        if (isset($param['whereIn'])) {
            $query->whereIn($param['whereInField'], $param['whereIn']);
        }

        $query->withCount($withCount);
        $query->orderBy($orderBy[0], $orderBy[1]);
        return ($flag == false) ? $query->first() : $query->get();
    }

    public function addPostCatalogueNamesToPosts($posts)
    {
        $postIds = $posts->pluck('id')->unique();
        $postCatalogueMapping = DB::table('post_catalogue_post')
            ->whereIn('post_id', $postIds)
            ->get()
            ->groupBy('post_id');
        $catalogueIds = $postCatalogueMapping->flatMap(fn($items) => $items->pluck('post_catalogue_id'))->unique();
        $postCatalogues = DB::table('post_catalogues')
            ->whereIn('id', $catalogueIds)
            ->get(['id', 'name'])
            ->keyBy('id');
        $posts->transform(function ($post) use ($postCatalogueMapping, $postCatalogues) {
            $catalogueIds = $postCatalogueMapping[$post->id] ?? collect();
            $post->array_post_catalogue_name = $catalogueIds->pluck('post_catalogue_id')->map(fn($id) => [
                'id' => $id,
                'name' => $postCatalogues[$id]->name ?? null
            ])->filter()->values();
            return $post;
        });
        return $posts;
    }
}
