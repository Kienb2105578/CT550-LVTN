<?php

namespace App\Repositories;

use App\Models\ProductVariant;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class ProductVariantRepository extends BaseRepository implements ProductVariantRepositoryInterface
{
    protected $model;

    public function __construct(
        ProductVariant $model
    ) {
        $this->model = $model;
    }

    public function findVariant($code, $productId)
    {
        $code = trim($code);
        return $this->model->where([
            ['code', '=', $code],
            ['product_id', '=', $productId]
        ])
            ->first();
    }

    public function getVariantInfo($variantId)
    {
        return $this->model->select('name', 'uuid')
            ->where('id', $variantId)
            ->first();
    }

    public function findProductVariant($productId, $uuid)
    {
        return $this->model->where([
            ['product_id', '=', $productId],
            ['uuid', '=', $uuid]
        ])->first();
    }
}
