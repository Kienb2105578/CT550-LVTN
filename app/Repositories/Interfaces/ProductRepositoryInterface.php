<?php

namespace App\Repositories\Interfaces;

/**
 * Interface ProductServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductRepositoryInterface
{
    public function addProductCatalogueNamesToProducts($products);
    public function getProductById(int $id);
    public function getPopularProducts();
    public function getAllProducts();
    public function addProductCanonicalToReviews($reviews);
    public function getAllProductCatalogues();
}
