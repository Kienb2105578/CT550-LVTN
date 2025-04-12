<?php

namespace App\Repositories\Interfaces;

/**
 * Interface ProductServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductCatalogueRepositoryInterface
{
    public function getProductCatalogueById(int $id = 0);
    public function all(array $relation = [], string $selectRaw = '');
}
