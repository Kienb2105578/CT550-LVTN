<?php

namespace App\Repositories\Interfaces;

/**
 * Interface PostServiceInterface
 * @package App\Services\Interfaces
 */
interface PostRepositoryInterface
{
    public function getPostById(int $id = 0);
    public function addPostCatalogueNamesToPosts($posts);
    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = [],
    );
}
