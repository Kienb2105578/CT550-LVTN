<?php

namespace App\Repositories;

use App\Models\AttributeCatalogue;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class AttributeCatalogueRepository extends BaseRepository implements AttributeCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(
        AttributeCatalogue $model
    ) {
        $this->model = $model;
    }



    public function getAttributeCatalogueById(int $id = 0)
    {
        return $this->model->select([
            'attribute_catalogues.id',
            'attribute_catalogues.parent_id',
            'attribute_catalogues.image',
            'attribute_catalogues.icon',
            'attribute_catalogues.album',
            'attribute_catalogues.publish',
            'attribute_catalogues.follow',
            'attribute_catalogues.name',
            'attribute_catalogues.description',
            'attribute_catalogues.content',
            'attribute_catalogues.meta_title',
            'attribute_catalogues.meta_keyword',
            'attribute_catalogues.meta_description',
            'attribute_catalogues.canonical',
        ])
            ->find($id);
    }
    public function getAll()
    {
        return $this->model->get();
    }
    public function getAttributeCatalogueWhereIn($whereIn, $whereInField = 'id')
    {
        return $this->model->select([
            'attribute_catalogues.id',
            'attribute_catalogues.name',
        ])
            ->where([config('apps.general.defaultPublish')])
            ->whereIn($whereInField, $whereIn)
            ->get();
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
