<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface as ProductVariantAttributeRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Illuminate\Pagination\Paginator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    protected $productRepository;
    protected $routerRepository;
    protected $productVariantAttributeRepository;
    protected $promotionRepository;
    protected $attributeCatalogueRepository;
    protected $attributeRepository;
    protected $productCatalogueService;

    public function __construct(
        ProductRepository $productRepository,
        RouterRepository $routerRepository,
        ProductVariantAttributeRepository $productVariantAttributeRepository,
        PromotionRepository $promotionRepository,
        AttributeCatalogueRepository $attributeCatalogueRepository,
        AttributeRepository $attributeRepository,
        ProductCatalogueService $productCatalogueService,
    ) {
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->promotionRepository = $promotionRepository;
        $this->productVariantAttributeRepository = $productVariantAttributeRepository;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->controllerName = 'ProductController';
    }

    private function whereRaw($request, $productCatalogue = null)
    {
        $rawCondition = [];

        if ($request->integer('product_catalogue_id') > 0 || !is_null($productCatalogue)) {
            $catId = ($request->integer('product_catalogue_id') > 0) ?
                $request->integer('product_catalogue_id') :
                $productCatalogue->id;

            $rawCondition['whereRaw'] = [
                [
                    'products.id IN (
                    SELECT DISTINCT pcp.product_id
                    FROM product_catalogue_product pcp
                    JOIN product_catalogues pc ON pcp.product_catalogue_id = pc.id
                    WHERE pc.lft >= (SELECT lft FROM product_catalogues WHERE id = ?)
                    AND pc.rgt <= (SELECT rgt FROM product_catalogues WHERE id = ?)
                )',
                    [$catId, $catId]
                ]
            ];
        }

        return $rawCondition;
    }



    public function paginate($request, $productCatalogue = null, $page = 1, $extend = [])
    {
        if (!is_null($productCatalogue)) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $perPage = (!is_null($productCatalogue))  ? 24 : 24;

        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),

        ];
        $paginationConfig = [
            'path' => ($extend['path']) ?? 'product/index',
            'groupBy' => $this->paginateSelect()
        ];

        $orderBy = ['products.id', 'DESC'];

        $relations = ['product_catalogues'];

        $rawQuery = $this->whereRaw($request, $productCatalogue);


        $products = $this->productRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            $paginationConfig,
            $orderBy,
            [],
            $relations,
            $rawQuery
        );

        return $products;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $product = $this->createProduct($request);
            if ($product->id > 0) {
                $this->updateCatalogueForProduct($product, $request);
                $this->createRouter($product, $request, $this->controllerName);
                if ($request->input('attribute')) {
                    $this->createVariant($product, $request);
                }
                $this->productCatalogueService->setAttribute($product);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $product = $this->uploadProduct($id, $request);
            if ($product) {
                $this->updateCatalogueForProduct($product, $request);
                $this->updateRouter(
                    $product,
                    $request,
                    $this->controllerName,
                );

                $product->product_variants()->each(function ($variant) {
                    $variant->attributes()->detach();
                    $variant->delete();
                });

                if ($request->input('attribute')) {
                    $this->createVariant($product, $request);
                }

                $this->productCatalogueService->setAttribute($product);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->delete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\ProductController'],
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }


    private function qrCode($request)
    {
        $canonical = write_url($request->input('canonical'));
        $name = str_replace('-', '_', $request->input('canonical'));
        $path = public_path('qrcodes/' . $name . '.jpg');
        $qrCode = QrCode::size(400)->generate($canonical);

        return $qrCode;
    }

    private function createVariant($product, $request)
    {
        $payload = $request->only(['variant', 'productVariant', 'attribute']);
        $variant = $this->createVariantArray($payload, $product);

        foreach ($variant as $key => &$var) {
            $var['name'] = $payload['productVariant']['name'][$key] ?? null;
        }

        $variants = $product->product_variants()->createMany($variant);

        $variantsId = $variants->pluck('id');
        $variantAttribute = [];
        $attributeCombines = $this->comebineAttribute(array_values($payload['attribute']));

        if (count($variantsId)) {
            foreach ($variantsId as $key => $val) {
                if (count($attributeCombines)) {
                    foreach ($attributeCombines[$key] as  $attributeId) {
                        $variantAttribute[] = [
                            'product_variant_id' => $val,
                            'attribute_id' => $attributeId,
                        ];
                    }
                }
            }
        }

        $variantAttribute = $this->productVariantAttributeRepository->createBatch($variantAttribute);
    }

    private function comebineAttribute($attributes = [], $index = 0)
    {
        if ($index === count($attributes)) return [[]];

        $subCombines = $this->comebineAttribute($attributes, $index + 1);
        $combines = [];
        foreach ($attributes[$index] as $key => $val) {
            foreach ($subCombines as $keySub => $valSub) {
                $combines[] = array_merge([$val], $valSub);
            }
        }
        return $combines;
    }

    private function sortString($string = ''): string
    {
        $extract = array_filter(array_map('trim', explode(',', $string)));
        sort($extract, SORT_NUMERIC);
        return implode(',', $extract);
    }

    private function createVariantArray($payload, $product): array
    {
        $variant = [];

        if (!empty($payload['variant']['sku'])) {
            foreach ($payload['variant']['sku'] as $key => $sku) {
                $variantIds = $payload['productVariant']['id'][$key] ?? '';
                $sortedCode = $this->sortString($variantIds);

                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . $variantIds);

                $variant[] = [
                    'uuid' => $uuid,
                    'code' => $sortedCode,
                    'quantity' => $payload['variant']['quantity'][$key] ?? '',
                    'sku' => $sku,
                    'price' => !empty($payload['variant']['price'][$key]) ? convert_price($payload['variant']['price'][$key]) : '',
                    'album' => $payload['variant']['album'][$key] ?? '',
                    'user_id' => Auth::id(),
                ];
            }
        }

        return $variant;
    }

    private function createProduct($request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = convert_price(($payload['price']) ?? 0);
        $payload['attributeCatalogue'] = $this->formatJson($request, 'attributeCatalogue');
        $payload['attribute'] = $request->input('attribute');
        $payload['variant'] = $this->formatJson($request, 'variant');

        $payload['qrcode'] = $this->qrCode($request);
        $product = $this->productRepository->create($payload);
        return $product;
    }

    private function uploadProduct($id, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = convert_price($payload['price']);
        if (!isset($payload['attribute'])) {
            $payload['attribute'] = null;
        }
        $payload['qrcode'] = $this->qrCode($request);

        $imagePath = $payload['image'] ?? null;
        if (!empty($imagePath)) {
            $features = $this->extractImageFeatures($imagePath);
            $payload['features'] = $features;
        } else {
            $payload['features'] = [];
        }
        $payload['features'] = json_encode($features);
        return $this->productRepository->update($id, $payload);
    }

    private function extractImageFeatures($imagePath)
    {
        $imagePath = ltrim($imagePath, '/');
        $imagePath = public_path($imagePath);
        $imagePath = str_replace('\\', '/', $imagePath);
        $imagePath = rtrim($imagePath, '/');
        $imagePath = urldecode($imagePath);

        if (file_exists($imagePath)) {
            // Cập nhật lệnh gọi Python
            $pythonScriptPath = storage_path('app/python/extract_features.py');
            $command = "python $pythonScriptPath \"" . escapeshellarg($imagePath) . "\"";  // Sử dụng python3 thay cho python
            // Lấy cả stderr và stdout
            $output = shell_exec($command);  // Lệnh này sẽ lấy cả output và lỗi
        }

        $output = preg_replace('/\e\[[0-9;]*m/', '', $output);  // Loại bỏ mã màu
        $output = preg_replace('/\d+\/\d+.*\n/', '', $output);   // Loại bỏ tiến trình
        $features = json_decode($output, true);
        return $features;
    }


    private function updateCatalogueForProduct($product, $request)
    {
        $product->product_catalogues()->sync($this->catalogue($request));
    }


    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->product_catalogue_id]));
        }
        return [$request->product_catalogue_id];
    }


    private function paginateSelect()
    {
        return [
            'products.id',
            'products.publish',
            'products.image',
            'products.order',
            'products.price',
            'products.quantity',
            'products.name',
            'products.description',
            'products.content',
            'products.canonical'
        ];
    }

    private function payload()
    {
        return [
            'follow',
            'publish',
            'image',
            'album',
            'quantity',
            'features',
            'price',
            'made_in',
            'code',
            'product_catalogue_id',
            'attributeCatalogue',
            'attribute',
            'variant',
            'name',
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical'
        ];
    }


    public function combineProductAndPromotion($productId, $products, $flag = false)
    {

        $promotions = $this->promotionRepository->findByProduct($productId);

        if ($promotions) {

            if ($flag == true) {
                $products->promotions = ($promotions[0]) ?? [];
                return $products;
            }

            foreach ($products as $index => $product) {
                foreach ($promotions as $key => $promotion) {
                    if ($promotion->product_id == $product->id) {
                        $products[$index]->promotions = $promotion;
                    }
                }
            }
        }
        return $products;
    }

    public function getAttribute($product, $language)
    {
        $product->attributeCatalogue = [];
        if (isset($product->attribute) && !is_null($product->attribute)) {
            $attributeCatalogueId = array_keys($product->attribute);
            $attrCatalogues = $this->attributeCatalogueRepository->getAttributeCatalogueWhereIn($attributeCatalogueId, 'attribute_catalogues.id', $language);
            /* ---- */
            $attributeId = array_merge(...$product->attribute);
            $attrs = $this->attributeRepository->findAttributeByIdArray($attributeId);
            if (!is_null($attrCatalogues)) {
                foreach ($attrCatalogues as $key => $val) {
                    $tempAttributes = [];
                    foreach ($attrs as $attr) {
                        if ($val->id == $attr->attribute_catalogue_id) {
                            $tempAttributes[] = $attr;
                        }
                    }
                    $val->attributes = $tempAttributes;
                }
            }

            $product->attributeCatalogue = $attrCatalogues;
        }
        return $product;
    }

    public function filter($request)
    {

        $perpage = $request->input('perpage');
        $param['priceQuery'] = $this->priceQuery($request);
        $param['attributeQuery'] = $this->attributeQuery($request);
        $param['rateQuery'] = $this->rateQuery($request);
        $param['productCatalogueQuery'] = $this->productCatalogueQuery($request);
        $query = $this->combineFilterQuery($param);
        $orderBy = $this->orderByQuery($query['join'], $request);

        $products = $this->productRepository->filter($query, $perpage, $orderBy);
        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->combineProductAndPromotion($productId, $products);
        }

        return $products;
    }

    private function orderByQuery($joins, $request)
    {
        $flag = false;
        $attributes = $request->input('attributes');
        if (is_array($joins) && count($joins)) {

            foreach ($joins as $key => $val) {
                if (is_null($val)) continue;
                if (count($val) && in_array('product_variants as pv', $val)) {
                    $flag = true;
                }
            }
        }
        // return ($flag == true && count($attributes) > 1) ? 'variant_id' : 'products.id';
        return 'products.id';
    }

    private function combineFilterQuery($param)
    {
        $query = [];

        foreach ($param as $array) {
            foreach ($array as $key => $value) {
                if (!isset($query[$key])) {
                    $query[$key] = [];
                }

                if (is_array($value)) {
                    $query[$key] = array_merge($query[$key], $value);
                } else {
                    $query[$key][] = $value;
                }
            }
        }
        return $query;
    }

    private function productCatalogueQuery($request)
    {

        $productCatalogueId = $request->input('productCatalogueId');
        $query['join'] = null;
        $query['whereRaw'] = null;
        if ($productCatalogueId > 0) {
            $query['join'] = [
                ['product_catalogue_product as pcp', 'pcp.product_id', '=', 'products.id']
            ];
            $query['whereRaw'] = [
                [
                    'pcp.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$productCatalogueId, $productCatalogueId]
                ]
            ];
        }
        return $query;
    }


    private function rateQuery($request)
    {
        $rates = $request->input('rate');
        $query['join'] = null;
        $query['having'] = null;

        if (!is_null($rates) && count($rates)) {
            $query['join'] = [
                ['reviews', 'reviews.reviewable_id', '=', 'products.id']
            ];
            $rateCondition = [];
            $bindings = [];

            foreach ($rates as $rate) {
                if ($rate != 5) {
                    $minRate = $rate;
                    $maxRate = $rate . '.9';
                    $rateCondition[] = '(AVG(reviews.score) >= ? AND AVG(reviews.score) <= ?)';
                    $bindings[] = $minRate;
                    $bindings[] = $maxRate;
                } else {
                    $rateCondition[] = 'AVG(reviews.score) = ?';
                    $bindings[] = 5;
                }
            }

            $query['where'] = function ($query) {
                $query->where('reviews.reviewable_type', '=', 'App\\Models\\Product');
            };
            $query['having'] = function ($query) use ($rateCondition, $bindings) {
                $query->havingRaw(implode(' OR ', $rateCondition), $bindings);
            };
        }
        return $query;
    }

    private function attributeQuery($request)
    {
        $attributes = $request->input('attributes');
        $query['select'] = null;
        $query['join'] = null;
        $query['where'] = null;

        if (!is_null($attributes) && count($attributes)) {


            $concatExpression = 'CONCAT(';
            $first = true;

            $query['join'] = [
                ['product_variants as pv', 'pv.product_id', '=', 'products.id'],
            ];
            foreach ($attributes as $key => $attribute) {
                $joinKey = 'tb' . $key;
                $query['join'][] = [
                    "product_variant_attribute as {$joinKey}",
                    "$joinKey.product_variant_id",
                    '=',
                    'pv.id'
                ];
                $query['where'][] = function ($query) use ($joinKey, $attribute) {
                    foreach ($attribute as $attr) {
                        $query->orWhere("$joinKey.attribute_id", '=', $attr);
                    }
                };

                if (!$first) {
                    $concatExpression .= ', ';
                } else {
                    $first = false;
                }

                $concatExpression .= "GROUP_CONCAT(DISTINCT $joinKey.attribute_id, ',')";
            }

            $concatExpression .= ' ) as attribute_concat';


            $query['select'] = "pv.price as variant_price, pv.sku as variant_sku, pv.id as variant_id, $concatExpression";
        }

        return $query;
    }


    private function priceQuery($request)
    {
        $price = $request->input('price');
        $priceMin = str_replace('đ', '', convert_price($price['price_min']));
        $priceMax = str_replace('đ', '', convert_price($price['price_max']));

        $query = [
            'select' => null,
            'join'   => [],
            'having' => null,
        ];

        if ($priceMax > $priceMin) {
            $query['join'][] = ['promotion_product_variant as ppv', 'ppv.product_id', '=', 'products.id'];
            $query['join'][] = ['promotions', 'ppv.promotion_id', '=', 'promotions.id'];

            $query['select'] = "
            (products.price - COALESCE((
                SELECT MAX(
                    CASE 
                        WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                        WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                        ELSE 0
                    END
                )
                FROM promotion_product_variant AS ppv
                LEFT JOIN promotions ON ppv.promotion_id = promotions.id
                WHERE ppv.product_id = products.id
                AND promotions.publish = 2
                AND promotions.endDate >= NOW()
            ), 0)) AS discounted_price
        ";

            $query['having'] = function ($query) use ($priceMin, $priceMax) {
                $query->havingRaw('discounted_price BETWEEN ? AND ?', [$priceMin, $priceMax]);
            };
        }

        return $query;
    }
}
