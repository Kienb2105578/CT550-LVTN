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
use App\Models\Product;
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
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id);

        $filters = $this->filter($productCatalogue);

        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, 1);

        $products = $this->productService->paginate(
            $request,
            1,
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
        ], 1);


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
            $filters = $this->productCatalogueService->getFilterList($groupedAttributes);
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
                $product_recommend[] = $this->productRepository->getProductById($id);
            }
            $products = $product_recommend;
        } elseif (empty($products)) {
            $products = $this->productRepository->search($keyword);
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

    public function searchProductByImage(Request $request)
    {
        $image = $request->file('image');
        $imagePath = $image->store('images', 'public');

        $pythonScript = storage_path('app/python/extract_features.py');
        $imageFullPath = storage_path('app/public/' . $imagePath);
        $imageFullPath  = str_replace('\\', '/', $imageFullPath);
        $command = "python $pythonScript $imageFullPath";
        $output = shell_exec($command);
        $output = preg_replace('/\e\[[0-9;]*m/', '', $output);
        $output = preg_replace('/\d+\/\d+.*\n/', '', $output);

        $features = json_decode($output, true);

        $similarProducts = $this->searchSimilarProducts($features);

        $products = $this->productRepository->updateProductTotalQuantity($similarProducts);
        $config = $this->config();
        $system = $this->system;
        $seo = [
            'meta_title' => 'Tìm kiếm bằng ảnh',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('tim-kiem-bang-ảnh')
        ];
        return view('frontend.product.catalogue.search', compact(
            'config',
            'seo',
            'system',
            'products'
        ));
    }

    private function searchSimilarProducts($uploadedImageFeatures)
    {
        $products = Product::all();
        $similarProducts = [];

        foreach ($products as $product) {
            if (empty($product->features)) {
                continue;
            }

            $storedFeatures = json_decode($product->features);


            if (!is_array($storedFeatures)) {
                continue;
            }

            $similarity = $this->cosineSimilarity($uploadedImageFeatures, $storedFeatures);
            if ($similarity > 0.5) {
                $similarProducts[] = $product;
            }
        }

        return $similarProducts;
    }


    private function cosineSimilarity($vecA, $vecB)
    {
        $dotProduct = array_sum(array_map(function ($a, $b) {
            return $a * $b;
        }, $vecA, $vecB));
        $magnitudeA = sqrt(array_sum(array_map(function ($a) {
            return $a * $a;
        }, $vecA)));
        $magnitudeB = sqrt(array_sum(array_map(function ($b) {
            return $b * $b;
        }, $vecB)));

        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
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
