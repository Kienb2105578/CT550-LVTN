<?php

namespace App\Repositories\Interfaces;

/**
 * Interface PurchaseOrderServiceInterface
 * @package App\Services\Interfaces
 */
interface PurchaseOrderRepositoryInterface
{
    public function update(int $id = 0, array $payload = []);
}
