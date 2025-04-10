<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Models\Cart as ModelsCart;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\ReviewRepositoryInterface as ReviewRepository;
use App\Services\Interfaces\WidgetServiceInterface  as WidgetService;
use App\Repositories\Interfaces\OrderRepositoryInterface  as OrderRepository;
use App\Models\System;
use Illuminate\Support\Facades\Http;
use Cart;
use Illuminate\Support\Facades\Log;

class ProductController extends FrontendController
{
    protected $language;
    protected $system;
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productService;
    protected $productRepository;
    protected $reviewRepository;
    protected $widgetService;
    protected $orderRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductService $productService,
        ProductRepository $productRepository,
        ReviewRepository $reviewRepository,
        WidgetService $widgetService,
        OrderRepository $orderRepository
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->reviewRepository = $reviewRepository;
        $this->widgetService = $widgetService;
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }


    public function index($id, $request)
    {
        $language = $this->language;
        $product = $this->productRepository->getProductById($id);
        $product = $this->productService->combineProductAndPromotion([$id], $product, true);

        $userId = auth()->id();
        $order_product = $this->orderRepository->checkUserHasOrderForProduct($userId, $id);

        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $this->language);
        $productId = $productCatalogue->products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $productCatalogue->products = $this->productService->combineProductAndPromotion($productId, $productCatalogue->products);
        }
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        $product = $this->productService->getAttribute($product, $this->language);
        try {
            $apiUrl = 'http://127.0.0.1:5555/api/product-recommendations';
            $response = Http::timeout(5)->get($apiUrl, ['id' => $id]);

            if ($response->successful()) {
                $relatedProducts = $response->json('related_products');
            } else {
                $relatedProducts = [];
            }
        } catch (\Exception $e) {
            $relatedProducts = [];
            Log::error('Lỗi khi gọi API: ' . $e->getMessage());
        }
        $id_Product = $relatedProducts;
        $product_recommend = [];
        foreach ($id_Product as $id) {
            $product_recommend[] = $this->productRepository->getProductById($id);
        }
        $productId = $id_Product;
        if (count($productId) && !is_null($productId)) {
            $product_recommend = $this->productService->combineProductAndPromotion($productId, $product_recommend);
        }

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'products-hl', 'promotion' => true],
        ], $this->language);

        $widgets['products-hl']->object = $this->productRepository->widgetProductTotalQuantity($widgets['products-hl']->object);
        $productCatalogue->products = $this->productRepository->updateProductTotalQuantity($productCatalogue->products);
        $product_recommend = $this->productRepository->updateProductTotalQuantity($product_recommend);
        $config = $this->config();
        $system = $this->system;
        $seo = seo($product);
        return view('frontend.product.product.index', compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'productCatalogue',
            'product',
            'widgets',
            'order_product',
            'product_recommend'
        ));
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'js' => [
                'frontend/core/library/cart.js',
                'frontend/core/library/product.js',
                'frontend/core/library/review.js'
            ],
            'css' => [
                'frontend/core/css/product.css',
            ]
        ];
    }
}
