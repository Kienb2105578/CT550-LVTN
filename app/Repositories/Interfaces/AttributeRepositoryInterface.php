<?php

namespace App\Repositories\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface AttributeRepositoryInterface
{
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
    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],
    );
    public function delete(int $id = 0);
    public function update(int $id = 0, array $payload = []);
    public function create(array $payload = []);
    public function addAttributeCatalogueNamesToAttributes($attributes);
    public function getAttributeById(int $id = 0);
}
