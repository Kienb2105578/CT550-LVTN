<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\SlideRepositoryInterface  as SlideRepository;
use App\Repositories\Interfaces\SystemRepositoryInterface  as SystemRepository;
use App\Services\Interfaces\WidgetServiceInterface  as WidgetService;
use App\Services\Interfaces\SlideServiceInterface  as SlideService;
use App\Services\Interfaces\ProductServiceInterface  as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Enums\SlideEnum;
use App\Events\TestEvent;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Gloudemans\Shoppingcart\Facades\Cart;


class HomeController extends FrontendController
{
    protected $language;
    protected $slideRepository;
    protected $systemRepository;
    protected $widgetService;
    protected $slideService;
    protected $productService;
    protected $system;
    protected $productRepository;

    public function __construct(
        SlideRepository $slideRepository,
        WidgetService $widgetService,
        SlideService $slideService,
        SystemRepository $systemRepository,
        ProductRepository $productRepository,
        ProductService $productService,
    ) {
        $this->slideRepository = $slideRepository;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        $this->systemRepository = $systemRepository;
        $this->productRepository = $productRepository;
        $this->productService = $productService;
        parent::__construct(
            $systemRepository,
        );
    }

    public function index()
    {


        $config = $this->config();
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'product', 'children' => true, 'promotion' => TRUE, 'object' => TRUE],
            ['keyword' => 'flash-sale', 'promotion' => true],
            ['keyword' => 'posts', 'object' => true],
        ], $this->language);

        try {
            $id = Auth::guard('customer')->check() ? Auth::guard('customer')->user()->id : null;
            $apiUrl = 'http://127.0.0.1:5555/api/customer-recommendations';

            $response = Http::timeout(3)->get($apiUrl, $id ? ['customer_id' => $id] : []);

            if ($response->successful()) {
                $relatedProducts = $response->json('related_products') ?? $response->json('popular_products');
            } else {
                $relatedProducts = [];
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi API: ' . $e->getMessage());
            $relatedProducts = []; // Nếu lỗi thì gán rỗng
        }

        // Nếu không có sản phẩm đề xuất từ API, thì lấy sản phẩm phổ biến
        if (empty($relatedProducts)) {
            $relatedProducts = $this->productRepository->getPopularProducts(1)->pluck('id')->toArray();
        }

        // Lấy thông tin chi tiết sản phẩm
        $product_recommend = [];
        foreach ($relatedProducts as $id) {
            $product_recommend[] = $this->productRepository->getProductById($id, 1);
        }

        // Nếu vẫn không có sản phẩm thì lấy danh sách sản phẩm phổ biến làm mặc định
        if (empty($product_recommend)) {
            $product_recommend = $this->productRepository->getPopularProducts(1);
        }

        $productId =  $relatedProducts;

        if (count($productId) && !is_null($productId)) {
            $product_recommend = $this->productService->combineProductAndPromotion($productId, $product_recommend);
        }

        $product_new = $this->productRepository->getLatestProducts();

        $product_d = $product_new->pluck('id')->toArray();
        if (count($product_d) && !is_null($product_d)) {
            $product_new = $this->productService->combineProductAndPromotion($product_d, $product_new);
        }

        foreach ($widgets['product']->object as $key => $category) {
            $widgets['product']->object->products = $this->productRepository->widgetProductTotalQuantity($category->products);
        }
        $product_new = $this->productRepository->updateProductTotalQuantity($product_new);
        $product_recommend = $this->productRepository->updateProductTotalQuantity($product_recommend);

        $slides = $this->slideService->getSlide([SlideEnum::BANNER, SlideEnum::MAIN, 'banner'], $this->language);
        $system = $this->system;
        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];
        return view('frontend.homepage.home.index', compact(
            'config',
            'slides',
            'widgets',
            'seo',
            'system',
            'product_recommend',
            'product_new'
        ));
    }

    public function ckfinder()
    {
        return view('frontend.homepage.home.ckfinder');
    }



    private function config()
    {
        return [
            'language' => $this->language,
            'css' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css'
            ],
            'js' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                'https://getuikit.com/v2/src/js/components/sticky.js'
            ]
        ];
    }
}
