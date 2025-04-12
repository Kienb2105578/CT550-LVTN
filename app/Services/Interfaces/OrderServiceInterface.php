<?php

namespace App\Services\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface OrderServiceInterface
{
    public function paginate($request);
    public function statistic();
    public function getOrderItemImage($order);
    public function create($request);
}
