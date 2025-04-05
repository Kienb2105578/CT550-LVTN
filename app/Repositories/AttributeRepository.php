<?php

namespace App\Repositories;

use App\Models\Attribute;
use App\Repositories\Interfaces\AttributeRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class AttributeRepository extends BaseRepository implements AttributeRepositoryInterface
{
    protected $model;

    public function __construct(
        Attribute $model
    ) {
        $this->model = $model;
    }



    public function getAttributeById(int $id = 0)
    {
        return $this->model->select(
            [
                'attributes.id',
                'attributes.attribute_catalogue_id',
                'attributes.image',
                'attributes.publish',
                'attributes.name',
                'attributes.description',
                'attributes.canonical',
            ]
        )
            ->with('attribute_catalogues')
            ->find($id);
    }


    public function searchAttributes(string $keyword = '', array $option = [])
    {
        $query = $this->model
            ->select(['attributes.id', 'attributes.name', 'attributes.attribute_catalogue_id']) // Chọn cột trực tiếp
            ->where('attributes.attribute_catalogue_id', $option['attributeCatalogueId'])
            ->where('attributes.name', 'like', '%' . $keyword . '%');

        $result = $query->get();
        return $result;
    }


    public function findAttributeByIdArray(array $attributeArray = [])
    {
        $query = $this->model->select([
            'attributes.id',
            'attributes.attribute_catalogue_id',
            'attributes.image',
            'attributes.name',
        ]);
        $defaultPublish = config('apps.general.defaultPublish');
        if (is_array($defaultPublish) && count($defaultPublish) === 3) {
            $query->where(...$defaultPublish);
        } elseif (is_string($defaultPublish)) {
            $query->where($defaultPublish);
        }
        $query->whereIn('attributes.id', $attributeArray);
        $result = $query->get();
        return $result;
    }



    public function addAttributeCatalogueNamesToAttributes($attributes)
    {
        $attributeIds = $attributes->pluck('id')->unique();
        $attributeCatalogueMapping = DB::table('attribute_catalogue_attribute')
            ->whereIn('attribute_id', $attributeIds)
            ->get()
            ->groupBy('attribute_id');
        $catalogueIds = $attributeCatalogueMapping->flatMap(fn($items) => $items->pluck('attribute_catalogue_id'))->unique();
        $attributeCatalogues = DB::table('attribute_catalogues')
            ->whereIn('id', $catalogueIds)
            ->get(['id', 'name'])
            ->keyBy('id');
        $attributes->transform(function ($attribute) use ($attributeCatalogueMapping, $attributeCatalogues) {
            $catalogueIds = $attributeCatalogueMapping[$attribute->id] ?? collect();
            $attribute->array_attribute_catalogue_name = $catalogueIds->pluck('attribute_catalogue_id')->map(fn($id) => [
                'id' => $id,
                'name' => $attributeCatalogues[$id]->name ?? null
            ])->filter()->values();
            return $attribute;
        });
        return $attributes;
    }



    public function findAttributeProductVariant($attributeId = [], $productCatalogueId = 0)
    {

        return $this->model->select([
            'attributes.id'
        ])
            ->join('product_variant_attribute as tb2', 'tb2.attribute_id', '=', 'attributes.id')
            ->join('product_variants as tb3', 'tb3.id', '=', 'tb2.product_variant_id')
            ->join('product_catalogue_product as tb4', 'tb4.product_id', '=', 'tb3.product_id')
            ->where('tb4.product_catalogue_id', '=',  $productCatalogueId)
            ->whereIn('attributes.id', $attributeId)
            ->distinct()
            ->pluck('attributes.id');
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
