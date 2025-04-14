<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */

    public $bindings = [
        'App\Repositories\Interfaces\CustomerRepositoryInterface' => 'App\Repositories\CustomerRepository',
        'App\Repositories\Interfaces\SupplierRepositoryInterface' => 'App\Repositories\SupplierRepository',
        'App\Repositories\Interfaces\InventoryBatchRepositoryInterface' => 'App\Repositories\InventoryBatchRepository',
        'App\Repositories\Interfaces\StockMovementRepositoryInterface' => 'App\Repositories\StockMovementRepository',
        'App\Repositories\Interfaces\UserRepositoryInterface' => 'App\Repositories\UserRepository',
        'App\Repositories\Interfaces\UserCatalogueRepositoryInterface' => 'App\Repositories\UserCatalogueRepository',
        'App\Repositories\Interfaces\PostCatalogueRepositoryInterface' => 'App\Repositories\PostCatalogueRepository',
        'App\Repositories\Interfaces\GenerateRepositoryInterface' => 'App\Repositories\GenerateRepository',
        'App\Repositories\Interfaces\PermissionRepositoryInterface' => 'App\Repositories\PermissionRepository',
        'App\Repositories\Interfaces\PostRepositoryInterface' => 'App\Repositories\PostRepository',
        'App\Repositories\Interfaces\ProvinceRepositoryInterface' => 'App\Repositories\ProvinceRepository',
        'App\Repositories\Interfaces\DistrictRepositoryInterface' => 'App\Repositories\DistrictRepository',
        'App\Repositories\Interfaces\RouterRepositoryInterface' => 'App\Repositories\RouterRepository',
        'App\Repositories\Interfaces\ProductCatalogueRepositoryInterface' => 'App\Repositories\ProductCatalogueRepository',
        'App\Repositories\Interfaces\ProductRepositoryInterface' => 'App\Repositories\ProductRepository',
        'App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface' => 'App\Repositories\AttributeCatalogueRepository',
        'App\Repositories\Interfaces\AttributeRepositoryInterface' => 'App\Repositories\AttributeRepository',
        'App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface' => 'App\Repositories\ProductVariantAttributeRepository',
        'App\Repositories\Interfaces\SystemRepositoryInterface' => 'App\Repositories\SystemRepository',
        'App\Repositories\Interfaces\MenuCatalogueRepositoryInterface' => 'App\Repositories\MenuCatalogueRepository',
        'App\Repositories\Interfaces\MenuRepositoryInterface' => 'App\Repositories\MenuRepository',
        'App\Repositories\Interfaces\SlideRepositoryInterface' => 'App\Repositories\SlideRepository',
        'App\Repositories\Interfaces\PromotionRepositoryInterface' => 'App\Repositories\PromotionRepository',
        'App\Repositories\Interfaces\ProductVariantRepositoryInterface' => 'App\Repositories\ProductVariantRepository',
        'App\Repositories\Interfaces\OrderRepositoryInterface' => 'App\Repositories\OrderRepository',
        'App\Repositories\Interfaces\CartRepositoryInterface' => 'App\Repositories\CartRepository',
        'App\Repositories\Interfaces\ReviewRepositoryInterface' => 'App\Repositories\ReviewRepository',
        'App\Repositories\Interfaces\DistributionRepositoryInterface' => 'App\Repositories\DistributionRepository',
        'App\Repositories\Interfaces\StockTakingRepositoryInterface' => 'App\Repositories\StockTakingRepository',
        'App\Repositories\Interfaces\PurchaseOrderRepositoryInterface' => 'App\Repositories\PurchaseOrderRepository',
    ];

    public function register(): void
    {
        foreach ($this->bindings as $key => $val) {
            $this->app->bind($key, $val);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
