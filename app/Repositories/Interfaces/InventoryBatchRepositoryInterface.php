<?php

namespace App\Repositories\Interfaces;

/**
 * Interface InventoryBatchServiceInterface
 * @package App\Services\Interfaces
 */
interface InventoryBatchRepositoryInterface
{
    public function getInventoryWithProduct();
    public function getReport($catalogue_id, $startDate, $endDate);
}
