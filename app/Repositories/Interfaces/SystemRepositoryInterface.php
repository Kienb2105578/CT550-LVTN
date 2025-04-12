<?php

namespace App\Repositories\Interfaces;

/**
 * Interface SystemServiceInterface
 * @package App\Services\Interfaces
 */
interface SystemRepositoryInterface
{
    public function findByCondition(
        $condition = [],
        $flag = false,
        $relation = [],
        array $orderBy = ['id', 'desc'],
        array $param = [],
        array $withCount = [],
    );
    public function updateOrInsert(array $payload = [], array $condition = []);
    public function all(array $relation = [], string $selectRaw = '');
}
