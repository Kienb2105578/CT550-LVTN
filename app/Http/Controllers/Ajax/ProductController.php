<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ProductServiceInterface  as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface  as PromotionRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface  as AttributeRepository;
use App\Models\Language;
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
    protected $language;

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
            $locale = app()->getLocale(); // vn en cn
            $this->language = 1;
            return $next($request);
        });
    }

    public function updateProductQuantity(Request $request)
    {
        $totalQuantity = $request->input('total_quantity', 0); // Lấy tổng số lượng từ request
        return response()->json([
            'updated_quantity' => $totalQuantity // Trả về tổng số lượng đã tính
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

        $variant = $this->productVariantRepository->findVariant($attributeId, $get['product_id'], $get['language_id']);

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
            $html .= '<div class="uk-grid uk-grid-medium">';
            foreach ($products as  $product) {
                $name = $product->name;
                Log::info($name);
                $canonical = write_url($product->canonical);
                $image = image($product->image);
                $price = getPrice($product);
                $catName = $product->product_catalogues->first()->name;
                $review = getReview($product);
                if (isset($product->attribute_concat)) {
                    $attributes = substr($product->attribute_concat, 0, -1);
                }

                $html .= '<div class="uk-width-large-1-4 mb20">';
                $html .= '<div class="product-item product">';
                // if ($price['percent'] > 0) {
                //     $html .= "<div class='badge badge-bg-1'>-{$price['percent']}%</div>";
                // }
                $html .= "<a href='$canonical' class='image img-scaledown img-zoomin'><img src='$image' alt='$product->name'></a>";
                $html .= '<div class="info">';


                $html .= "<div class='category-title'><a href='$canonical' title='$product->name'>$catName</a></div>";
                $html .= "<h3 class='title product-title-filter'><a href='$canonical' title='$product->name'>$product->name</a></h3>";
                $html .= '<div class="rating">';
                $html .= '<div class="uk-flex uk-flex-middle">';
                $html .= '<div class="star-rating">';
                $html .= '<div class="stars" style="--star-width: ' . $review['star'] . '%"></div>';
                $html .= '</div>';
                $html .= '<span class="rate-number">(' . $review['count'] . ')</span>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="product-group">';
                $html .= '<div class="uk-flex uk-flex-middle uk-flex-space-between">';
                $html .= $price['html'];
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';

            $html .= $products->links('pagination::bootstrap-4');
        } else {
            $html .= '<div class="no-result">Không có sản phẩm phù hợp</div>';
        }


        return $html;
    }

    public function wishlist(Request $request)
    {
        $id = $request->input('id');
        $wishlist = Cart::instance('wishlist')->content();
        $itemIndex = $wishlist->search(function ($item, $rowId) use ($id) {
            return $item->id === $id;
        });

        $response['code'] = 0;
        $response['message'] = '';
        if ($itemIndex !== false) {
            Cart::instance('wishlist')->remove($wishlist->keyBy('id')[$id]->rowId);

            $response['code'] = 1;
            $response['message'] = 'Sản phẩm đã được xóa khỏi danh sách yêu thích';
        } else {
            Cart::instance('wishlist')->add([
                'id' => $id,
                'name' => 'wishlist item',
                'qty' => 1,
                'price' => 0,
            ]);

            $response['code'] = 2;
            $response['message'] = 'Đã thêm sản phẩm vào danh sách yêu thích';
        }

        return response()->json($response);
    }
}
