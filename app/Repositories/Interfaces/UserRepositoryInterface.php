<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface UserRepositoryInterface
{
    public function find($id);
    public function getUserByEmail(string $email);
    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],
    );
}
