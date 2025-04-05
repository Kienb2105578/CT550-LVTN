<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Models\System;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Cart;

class ProductCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productService;
    protected $widgetService;
    protected $productRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductService $productService,
        ProductRepository $productRepository,
        WidgetService $widgetService,
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->widgetService = $widgetService;
        $this->productRepository = $productRepository;
        parent::__construct();
    }


    public function index($id, $request, $page = 1)
    {
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);

        $filters = $this->filter($productCatalogue);

        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);

        $products = $this->productService->paginate(
            $request,
            $this->language,
            $productCatalogue,
            $page,
            ['path' => $productCatalogue->canonical],
        );

        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'products-hl', 'promotion' => true],
        ], $this->language);


        $widgets['products-hl']->object = $this->productRepository->widgetProductTotalQuantity($widgets['products-hl']->object);
        $products = $this->productRepository->updateProductTotalQuantity($products);

        $config = $this->config();
        $system = $this->system;
        $seo = seo($productCatalogue, $page);
        return view('frontend.product.catalogue.index', compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'productCatalogue',
            'products',
            'filters',
            'widgets',
        ));
    }
    // public function main(Request $request, $page = 1)
    // {

    //     $productCatalogues = $this->productCatalogueRepository->all();
    //     $allFilters = [];

    //     foreach ($productCatalogues as $productCatalogue) {
    //         $filters = $this->filter($productCatalogue);

    //         if ($filters instanceof \Illuminate\Support\Collection) {
    //             $filters = $filters->toArray(); // Chuyển Collection thành array
    //         }

    //         if (!empty($filters)) {
    //             $allFilters = array_merge($allFilters, $filters);
    //         }
    //     }

    //     // Chuyển về mảng và loại bỏ trùng
    //     $allFilters = array_values(array_unique($allFilters, SORT_REGULAR));

    //     dd(collect($allFilters)); // Nếu muốn giữ Collection, có thể chuyển lại

    //     $products = $this->productService->paginate(
    //         $request,
    //         $this->language,
    //         null,
    //         $page,
    //         ['path' => route('product.catalogue.main')],
    //     );

    //     $productId = $products->pluck('id')->toArray();
    //     if (count($productId) && !is_null($productId)) {
    //         $products = $this->productService->combineProductAndPromotion($productId, $products);
    //     }

    //     $widgets = $this->widgetService->getWidget([
    //         ['keyword' => 'products-hl', 'promotion' => true],
    //     ], $this->language);


    //     $widgets['products-hl']->object = $this->productRepository->widgetProductTotalQuantity($widgets['products-hl']->object);
    //     $products = $this->productRepository->updateProductTotalQuantity($products);

    //     $config = $this->config();
    //     $system = $this->system;
    //     $seo = [
    //         'meta_title' => 'Sản phẩm',
    //         'meta_keyword' => '',
    //         'meta_description' => '',
    //         'meta_image' => '',
    //         'canonical' => route('product.catalogue.main')
    //     ];
    //     return view('frontend.product.catalogue.main', compact(
    //         'config',
    //         'seo',
    //         'system',
    //         'products',
    //         'widgets',
    //         'filters'
    //     ));
    // }

    private function filter($productCatalogue)
    {
        $filters = null;
        $children = $this->productCatalogueRepository->getChildren($productCatalogue);
        $groupedAttributes = [];
        foreach ($children as $child) {
            if (isset($child->attribute) && !is_null($child->attribute) && count($child->attribute)) {
                foreach ($child->attribute as $key => $value) {
                    if (!isset($groupedAttributes[$key])) {
                        $groupedAttributes[$key] = [];
                    }
                    $groupedAttributes[$key][] = $value;
                }
            }
        }
        foreach ($groupedAttributes as $key => $value) {
            $groupedAttributes[$key] = array_merge(...$value);
        }

        if (isset($groupedAttributes) && !is_null($groupedAttributes) &&  count($groupedAttributes)) {
            $filters = $this->productCatalogueService->getFilterList($groupedAttributes, $this->language);
        }
        return $filters;
    }


    public function search(Request $request)
    {

        $keyword = $request->input('keyword');
        $products = [];

        try {
            $apiUrl = 'http://127.0.0.1:5555/api/search_products';
            $response = Http::timeout(5)->get($apiUrl, ['keyword' => $keyword]);

            if ($response->successful()) {
                $products = $response->json('related_products');
            }
            Log::info("VÔ TÌM KIẾM");
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi API tìm kiếm: ' . $e->getMessage());
        }

        if (!empty($products)) {
            $product_recommend = [];
            foreach ($products as $id) {
                $product_recommend[] = $this->productRepository->getProductById($id, 1);
            }
            $products = $product_recommend;
        } elseif (empty($products)) {
            $products = $this->productRepository->search($keyword, $this->language);
        }

        $productId = collect($products)->pluck('id')->toArray();
        if (!empty($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }

        $products = $this->productRepository->updateProductTotalQuantity($products);
        $config = $this->config();
        $system = $this->system;
        $carts = Cart::instance('shopping')->content();
        $seo = [
            'meta_title' => 'Tìm kiếm cho từ khóa: ' . $request->input('keyword'),
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('tim-kiem')
        ];
        return view('frontend.product.catalogue.search', compact(
            'config',
            'seo',
            'system',
            'products',
            'carts'
        ));
    }

    public function wishlist(Request $request)
    {

        $id = Cart::instance('wishlist')->content()->pluck('id')->toArray();

        $products = $this->productRepository->wishlist($id, $this->language);
        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }

        $config = $this->config();
        $system = $this->system;
        $carts = Cart::instance('shopping')->content();
        $seo = [
            'meta_title' => 'Danh sách yêu thích',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('tim-kiem')
        ];
        return view('frontend.product.catalogue.search', compact(
            'config',
            'seo',
            'system',
            'products',
            'carts'
        ));
    }


    private function config()
    {
        return [
            'language' => $this->language,
            'externalJs' => [
                '//code.jquery.com/ui/1.11.4/jquery-ui.js'
            ],
            'js' => [
                'frontend/core/library/filter.js',
            ],

        ];
    }
}
