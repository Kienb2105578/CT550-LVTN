<?php

namespace App\Repositories;

use App\Models\ProductCatalogue;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class ProductCatalogueRepository extends BaseRepository implements ProductCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(
        ProductCatalogue $model
    ) {
        $this->model = $model;
    }


    public function all(array $relation = [], string $selectRaw = '')
    {
        $query = $this->model->newQuery();

        if (!empty($relation)) {
            $query->with($relation);
        }

        if (!empty($selectRaw)) {
            $query->selectRaw($selectRaw);
        } else {
            $query->select('*');
        }

        return $query->get();
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

    public function getProductCatalogueById(int $id = 0)
    {
        return $this->model->select(
            [
                'product_catalogues.id',
                'product_catalogues.parent_id',
                'product_catalogues.lft',
                'product_catalogues.rgt',
                'product_catalogues.image',
                'product_catalogues.publish',
                'product_catalogues.attribute',
                'product_catalogues.name',
                'product_catalogues.description',
                'product_catalogues.canonical',
            ]
        )->find($id);
    }
    public function getChildren($productCatalogue)
    {
        return $this->model->select(
            [
                'product_catalogues.id',
                'product_catalogues.parent_id',
                'product_catalogues.lft',
                'product_catalogues.rgt',
                'product_catalogues.image',
                'product_catalogues.publish',
                'product_catalogues.attribute',
                'product_catalogues.name',
                'product_catalogues.description',
                'product_catalogues.canonical',
            ]
        )
            ->where('lft', '>=', $productCatalogue->lft)
            ->where('rgt', '<=', $productCatalogue->rgt)
            ->get();
    }

    public function getProductCatalogueByPublish($publish)
    {
        return $this->model->where('publish', $publish)->get();
    }
}
