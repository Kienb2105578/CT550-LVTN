<?php

namespace App\Repositories\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface MenuCatalogueRepositoryInterface
{
    public function all(array $relation = [], string $selectRaw = '');

    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],
    );
    public function forceDelete(int $id = 0);

    public function update(int $id = 0, array $payload = []);

    public function create(array $payload = []);

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 1,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = [],

    );
}
