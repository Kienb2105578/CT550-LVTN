<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface CustomerRepositoryInterface
{
    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],
    );
}
