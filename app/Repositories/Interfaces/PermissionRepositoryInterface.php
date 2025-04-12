<?php

namespace App\Repositories\Interfaces;

/**
 * Interface PermissionServiceInterface
 * @package App\Services\Interfaces
 */
interface PermissionRepositoryInterface
{
    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],
    );
    public function all(array $relation = [], string $selectRaw = '');
}
