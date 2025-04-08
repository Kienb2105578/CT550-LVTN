<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserCatalogueServiceInterface
 * @package App\Services\Interfaces
 */
interface CustomerCatalogueRepositoryInterface
{
    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],

    );
}
