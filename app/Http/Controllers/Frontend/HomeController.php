<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\SlideRepositoryInterface  as SlideRepository;
use App\Repositories\Interfaces\SystemRepositoryInterface  as SystemRepository;
use App\Services\Interfaces\SlideServiceInterface  as SlideService;
use App\Services\Interfaces\ProductServiceInterface  as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Enums\SlideEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class HomeController extends FrontendController
{
    protected $slideRepository;
    protected $systemRepository;
    protected $slideService;
    protected $productService;
    protected $system;
    protected $productRepository;
    protected $promotionRepository;

    public function __construct(
        SlideRepository $slideRepository,
        SlideService $slideService,
        SystemRepository $systemRepository,
        ProductRepository $productRepository,
        ProductService $productService,
        PromotionRepository $promotionRepository,
    ) {
        $this->slideRepository = $slideRepository;
        $this->slideService = $slideService;
        $this->systemRepository = $systemRepository;
        $this->productRepository = $productRepository;
        $this->productService = $productService;
        $this->promotionRepository = $promotionRepository;
        parent::__construct(
            $systemRepository,
        );
    }

    public function index()
    {
        // Cấu hình hệ thống
        $config = $this->config();



        /*
    |--------------------------------------------------------------------------
    | GỢI Ý SẢN PHẨM CHO KHÁCH HÀNG
    |--------------------------------------------------------------------------
    */
        try {
            $id = Auth::guard('customer')->check() ? Auth::guard('customer')->user()->id : null;
            $apiUrl = 'http://127.0.0.1:5555/api/customer-recommendations';

            // Gọi API gợi ý sản phẩm
            $response = Http::timeout(5)->get($apiUrl, $id ? ['customer_id' => $id] : []);
            if ($response->successful()) {
                $relatedProducts = $response->json('related_products') ?? $response->json('popular_products');
            } else {
                $relatedProducts = [];
            }
        } catch (\Exception $e) {
            $relatedProducts = [];
        }

        // Nếu không có kết quả từ AI thì fallback về sản phẩm phổ biến
        if (empty($relatedProducts)) {
            $relatedProducts = $this->productRepository->getPopularProducts()->pluck('id')->toArray();
        }

        // Lấy danh sách sản phẩm từ các ID được gợi ý
        $product_recommend = [];
        foreach ($relatedProducts as $id) {
            $product = $this->productRepository->getProductById($id);
            if ($product !== null) {
                $product_recommend[] = $product;
            }
        }

        // Nếu không có sản phẩm nào hợp lệ, tiếp tục fallback sản phẩm phổ biến
        if (empty($product_recommend)) {
            $product_recommend = $this->productRepository->getPopularProducts();
        }

        // Kết hợp khuyến mãi với danh sách sản phẩm gợi ý
        if (!empty($relatedProducts)) {
            $product_recommend = $this->productService->combineProductAndPromotion($relatedProducts, $product_recommend);
        }

        /*
    |--------------------------------------------------------------------------
    | SẢN PHẨM MỚI
    |--------------------------------------------------------------------------
    */
        $product_new = $this->productRepository->getLatestProducts();
        $product_d = $product_new->pluck('id')->toArray();
        if (!empty($product_d)) {
            $product_new = $this->productService->combineProductAndPromotion($product_d, $product_new);
        }

        /*
    |--------------------------------------------------------------------------
    | SLIDESHOW 
    |--------------------------------------------------------------------------
    */
        // Cập nhật tồn kho sản phẩm
        $product_new = $this->productRepository->updateProductTotalQuantity($product_new);
        $product_recommend = $this->productRepository->updateProductTotalQuantity($product_recommend);

        // Lấy slide hiển thị
        $slides = $this->slideService->getSlide([
            SlideEnum::BANNER,
            SlideEnum::MAIN,
            'banner'
        ]);

        /*
    |--------------------------------------------------------------------------
    | KHUYẾN MÃI NỔI BẬT
    |--------------------------------------------------------------------------
    */
        $promotion = $this->promotionRepository->getActivePromotionProducts();
        $product_promotion = [];

        foreach ($promotion as $id) {
            $product = $this->productRepository->getProductById($id);
            if ($product !== null) {
                $product_promotion[] = $product;
            }
        }

        $productId = $promotion->toArray();
        if (!empty($productId)) {
            $product_promotion = $this->productService->combineProductAndPromotion($productId, $product_promotion);
        }

        $product_promotion = $this->productRepository->updateProductTotalQuantity($product_promotion);
        $promotion_new = $this->promotionRepository->getLatestActivePromotion();

        /*
    |--------------------------------------------------------------------------
    | SEO & TRẢ VỀ VIEW
    |--------------------------------------------------------------------------
    */
        $system = $this->system;
        $seo = [
            'meta_title' => $system['seo_meta_title'],
            'meta_keyword' => $system['seo_meta_keyword'],
            'meta_description' => $system['seo_meta_description'],
            'meta_image' => $system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];

        return view('frontend.homepage.home.index', compact(
            'config',
            'slides',
            'seo',
            'system',
            'product_recommend',
            'product_new',
            'product_promotion',
            'promotion_new',
        ));
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
