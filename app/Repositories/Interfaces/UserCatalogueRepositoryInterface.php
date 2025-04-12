<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserCatalogueServiceInterface
 * @package App\Services\Interfaces
 */
interface UserCatalogueRepositoryInterface
{
    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],
    );
    public function all(array $relation = [], string $selectRaw = '');
    public function getActiveUserCatalogueList();
}
