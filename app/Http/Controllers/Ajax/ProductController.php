<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ProductServiceInterface  as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface  as PromotionRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface  as AttributeRepository;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cart;
use App\Repositories\Interfaces\CartRepositoryInterface  as CartRepository;


class ProductController extends Controller
{
    protected $productService;
    protected $productRepository;
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $attributeRepository;
    protected $cartRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        CartRepository $cartRepository,
        AttributeRepository $attributeRepository,
        ProductService $productService,
    ) {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productService = $productService;
        $this->cartRepository = $cartRepository;
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }

    public function updateProductQuantity(Request $request)
    {
        $totalQuantity = $request->input('total_quantity', 0);
        return response()->json([
            'updated_quantity' => $totalQuantity
        ]);
    }
    public function checkQuantity(Request $request)
    {
        $productId = $request->input('product_id');
        $requestedQuantity = (int) $request->input('quantity');
        $attributeIds = $request->input('attribute_id');
        $sortedAttributeIds = $attributeIds ? implode(',', collect(explode(',', $attributeIds))->sort()->toArray()) : '';

        $variant = $this->productRepository->getProductVariantByAttributes($productId, $attributeIds);
        $variantId = $variant ? $variant->id : null;
        $pro_van_qty = $this->productRepository->getTotalQuantityByProductAndVariant($productId, $variantId);

        $firstResult = $pro_van_qty->first();

        if (!$firstResult) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm chưa có hàng tồn kho. Vui lòng liên hệ người bán!'
            ]);
        }

        $totalQuantity = (int) $pro_van_qty->first()->total_quantity;

        if ($requestedQuantity > $totalQuantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Số lượng lớn hơn trong còn lại'
            ]);
        }

        $user = Auth::guard('customer')->user();
        if ($user) {

            $cart_customer_qty = $this->cartRepository->getQuantityByUserProductVariant($user->id, $productId, $variantId);
            $totalQuantityInCart = $cart_customer_qty + $requestedQuantity;

            if ($totalQuantityInCart > $totalQuantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Số lượng trong giỏ hàng và yêu cầu vượt quá tồn kho.'
                ]);
            }
        } else {
            $cart = Cart::instance('shopping')->content();

            $cart_customer_qty = 0;
            foreach ($cart as $item) {
                $cart_product_id = explode('_', $item->id)[0];
                if ($cart_product_id == $productId && $item->options->variant_id == $variantId) {
                    $cart_customer_qty += (int) $item->qty;
                }
            }
            $totalQuantityInCart = $cart_customer_qty + $requestedQuantity;

            if ($totalQuantityInCart > $totalQuantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Số lượng trong giỏ hàng và số lượng yêu cầu vượt quá số lượng tồn kho.'
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }




    public function checkQuantityCart(Request $request)
    {
        $productId = $request->input('product_id');
        $requestedQuantity = (int) $request->input('quantity');
        $attributeId = $request->input('attribute_id');
        $sortedAttributeIds = $attributeId ? implode(',', collect(explode(',', $attributeId))->sort()->toArray()) : '';
        Log::info($attributeId);
        $variant = $this->productRepository->getProductVariantByAttributes($productId, $sortedAttributeIds);
        $variantId = $variant ? $variant->id : null;
        $pro_van_qty = $this->productRepository->getTotalQuantityByProductAndVariant($productId, $variantId);
        log::info("tétt", ['$pro_van_qty', $pro_van_qty]);
        $totalQuantity = (int) $pro_van_qty->first()->total_quantity;

        if ($requestedQuantity > $totalQuantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Số lượng lớn hơn trong còn lại'
            ]);
        }

        return response()->json(['status' => 'ok']);
    }


    public function loadProductPromotion(Request $request)
    {
        $get = $request->input();
        $loadClass = loadClass($get['model']);

        if ($get['model'] == 'Product') {
            $condition = [];

            if (!empty($get['keyword'])) {
                $condition[] = ['products.name', 'LIKE', '%' . $get['keyword'] . '%'];
            }

            $objects = $loadClass->findProductForPromotion($condition);
        } elseif ($get['model'] == 'ProductCatalogue') {
            $conditionArray = [
                'keyword' => $get['keyword'] ?? null,
                'where' => []
            ];

            $objects = $loadClass->pagination(
                [
                    'product_catalogues.id',
                    'product_catalogues.name',
                ],
                $conditionArray,
                20,
                ['path' => 'product.catalogue.index'],
                ['product_catalogues.id', 'DESC'],
                [],
                []
            );
        } else {
            return response()->json([
                'error' => 'Invalid model type',
            ], 400);
        }

        return response()->json([
            'model' => $get['model'] ?? 'Product',
            'objects' => $objects,
        ]);
    }


    public function loadVariant(Request $request)
    {
        $get = $request->input();
        $attributeId = $get['attribute_id'];

        $attributeId = sortAttributeId($attributeId);

        $variant = $this->productVariantRepository->findVariant($attributeId, $get['product_id']);

        $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
        $variantPrice = getVariantPrice($variant, $variantPromotion);
        return response()->json([
            'variant' => $variant,
            'variantPrice' => $variantPrice,
            'variantPromotion' => $variantPromotion
        ]);
    }


    public function filter(Request $request)
    {
        $products = $this->productService->filter($request);
        Log::info("FILTER:", ['filter', $products]);

        $html = $this->renderFilterProduct($products);

        return response()->json([
            'data' => $html,
        ]);
    }

    public function renderFilterProduct($products)
    {
        $html = '';

        if (!is_null($products) && count($products)) {
            $html .= '<div class="row">';

            foreach ($products as $product) {
                $name = $product->name;
                Log::info($name);
                $canonical = write_url($product->canonical);
                $image = image($product->image);
                $price = getPrice($product);
                $catName = $product->product_catalogues->first()->name;
                $review = getReview($product);
                $attributes = isset($product->attribute_concat) ? substr($product->attribute_concat, 0, -1) : '';

                $html .= '<div class="col-md-3 mb-4">';
                $html .= '<div class="product-item product card h-100">';

                // Hiển thị phần trăm giảm giá nếu có
                // if ($price['percent'] > 0) {
                //     $html .= "<div class='badge bg-danger position-absolute top-0 start-0 m-2'>-{$price['percent']}%</div>";
                // }

                $html .= "<a href='$canonical' class='image img-scaledown img-zoomin position-relative d-block'>";
                $html .= "<img src='$image' alt='$product->name' class='card-img-top img-fluid'>";
                $html .= '</a>';

                $html .= '<div class="info card-body p-3 d-flex flex-column justify-content-between">';
                $html .= '<div class="mb-2">';

                // Danh mục
                $html .= "<div class='category-title mb-1'>";
                $html .= "<a href='$canonical' title='$product->name' class='text-muted small text-decoration-none'>$catName</a>";
                $html .= '</div>';

                // Tên sản phẩm
                $html .= "<h3 class='title product-title-filter h6 mb-2'>";
                $html .= "<a href='$canonical' title='$product->name' class='text-dark text-decoration-none'>$product->name</a>";
                $html .= '</h3>';

                $html .= '</div>'; // End top info

                // Rating
                $html .= '<div class="rating d-flex align-items-center">';
                $html .= '<div class="star-rating me-1">';
                $html .= "<div class='stars' style='--star-width: {$review['star']}%'></div>";
                $html .= '</div>';
                $html .= "<span class='rate-number small text-muted'>({$review['count']})</span>";
                $html .= '</div>';

                // Giá
                $html .= '<div class="product-group">';
                $html .= '<div class="d-flex justify-content-between align-items-center">';
                $html .= $price['html'];
                $html .= '</div>';
                $html .= '</div>'; // End product-group

                $html .= '</div>'; // End card-body
                $html .= '</div>'; // End product card
                $html .= '</div>'; // End column
            }

            $html .= '</div>'; // End row

            // Pagination bên ngoài vòng lặp
            $html .= '<div class="mt-3">';
            $html .= $products->links('pagination::bootstrap-4');
            $html .= '</div>';
        } else {
            $html .= '<div class="no-result">Không có sản phẩm phù hợp</div>';
        }

        return $html;
    }
}
