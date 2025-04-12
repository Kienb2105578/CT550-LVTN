<?php

namespace App\Repositories\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface MenuRepositoryInterface
{
    public function findByCondition(
        $condition = [],
        $flag = false,
        $relation = [],
        array $orderBy = ['id', 'desc'],
        array $param = [],
        array $withCount = [],
    );
    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],
    );
    public function forceDeleteByCondition(array $condition = []);
    public function update(int $id = 0, array $payload = []);
    public function create(array $payload = []);
    public function updateByWhere($condition = [], array $payload = []);
}
