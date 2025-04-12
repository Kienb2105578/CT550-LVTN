<?php

namespace App\Repositories\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface OrderRepositoryInterface
{
    public function newOrder($startDate, $endDate);
    public function getOrderById($id);
    public function getTotalRevenueReportTime($startDate, $endDate);
    public function getCustomerReportTime($startDate, $endDate);
    public function getReportTime($startDate, $endDate);
    public function getProductReportTime($startDate, $endDate);
}
