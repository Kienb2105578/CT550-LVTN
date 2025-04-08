<?php

namespace App\Repositories;

use App\Models\PostCatalogue;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface;
use App\Repositories\BaseRepository;


/**
 * Class UserService
 * @package App\Services
 */
class PostCatalogueRepository extends BaseRepository implements PostCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(
        PostCatalogue $model
    ) {
        $this->model = $model;
    }




    public function getPostCatalogueById(int $id = 0)
    {
        return $this->model->select(
            [
                'post_catalogues.id',
                'post_catalogues.parent_id',
                'post_catalogues.image',
                'post_catalogues.publish',
                'post_catalogues.lft',
                'post_catalogues.rgt',
                'post_catalogues.name',
                'post_catalogues.description',
                'post_catalogues.content',
                'post_catalogues.meta_title',
                'post_catalogues.meta_keyword',
                'post_catalogues.meta_description',
                'post_catalogues.canonical',
            ]
        )
            ->find($id);
    }
    public function breadcrumb($model, $language)
    {
        return $this->findByCondition([
            ['lft', '<=', $model->lft],
            ['rgt', '>=', $model->rgt],
            config('apps.general.defaultPublish')
        ], false, [], ['lft', 'asc']);
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
}
