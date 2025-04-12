<?php

namespace App\Repositories\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface AttributeCatalogueRepositoryInterface
{
    public function getAll();
    public function getAttributeCatalogueById(int $id = 0);
}
