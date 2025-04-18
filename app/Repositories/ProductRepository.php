<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductCatalogue;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class UserService
 * @package App\Services
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(
        Product $model
    ) {
        $this->model = $model;
    }

    public function search($keyword)
    {
        return $this->model->select(
            [
                'products.id',
                'products.product_catalogue_id',
                'products.image',
                'products.album',
                'products.publish',
                'products.quantity',
                'products.price',
                'products.code',
                'products.made_in',
                'products.attributeCatalogue',
                'products.attribute',
                'products.name',
                'products.description',
                'products.content',
                'products.canonical',
            ]
        )
            ->with([
                'product_catalogues',
                'product_variants' => function ($query) {
                    $query->with(['attributes']);
                },
                'reviews'
            ])
            ->where('products.publish', '=', 2)
            ->where('products.name', 'LIKE', '%' . $keyword . '%')
            ->get();
    }

    public function getProductQuantity($productId)
    {
        $product = $this->model->select('id', 'quantity')
            ->where('id', $productId)
            ->first();

        $variants = DB::table('product_variants')
            ->select('id', 'product_id', 'code', 'sku', 'quantity')
            ->where('product_id', $productId)
            ->get();

        return [
            'product' => [
                'id' => $product->id,
                'quantity' => $product->quantity,
            ],
            'variants' => $variants
        ];
    }

    public function getProductVariantByAttributes($productId, $attributeId)
    {
        $sortedAttributeId = implode(',', collect(explode(',', $attributeId))->sort()->toArray());
        $variants = DB::table('product_variants')
            ->where('product_id', $productId)
            ->get();
        foreach ($variants as $variant) {
            $sortedVariantCode = implode(',', collect(explode(',', $variant->code))->sort()->toArray());
            if ($sortedVariantCode === $sortedAttributeId) {
                return $variant;
            }
        }
        return null;
    }

    public function getTotalQuantityByProductAndVariant($productId, $variantId = null)
    {
        $query = DB::table('inventory_batches')->where('product_id', $productId);
        if ($variantId === null) {
            $query->whereNull('variant_id');
        } else {
            $query->where('variant_id', $variantId);
        }
        return $query->select('product_id', 'variant_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id', 'variant_id')
            ->get();
    }

    public function getTotalProduct()
    {
        return $this->model->whereNull('deleted_at')->count();
    }


    /**
     *
     *   Phương thức lấy thông tin sản phẩm theo ID
     *   Bao gồm thông tin danh mục, biến thể, thuộc tính và đánh giá
     *
     */
    public function getProductById(int $id = 0)
    {
        return $this->model->select([
            'products.id',
            'products.product_catalogue_id',
            'products.image',
            'products.album',
            'products.publish',
            'products.quantity',
            'products.price',
            'products.code',
            'products.made_in',
            'products.attributeCatalogue',
            'products.attribute',
            'products.variant',
            'products.qrcode',
            'products.name',
            'products.description',
            'products.content',
            'products.canonical',
            'product_catalogues.name as product_catalogue_name'
        ])
            ->join('product_catalogues', 'product_catalogues.id', '=', 'products.product_catalogue_id')
            ->whereNull('products.deleted_at')
            ->with([
                'product_variants' => function ($query) {
                    $query->with(['attributes']);
                },
                'reviews'
            ])
            ->find($id);
    }



    public function getProductName($productId)
    {
        $product = $this->getProductById($productId);

        return $product ? $product->name : null;
    }


    public function getPopularProducts()
    {
        return $this->model->select(
            [
                'products.id',
                'products.product_catalogue_id',
                'products.image',
                'products.album',
                'products.publish',
                'products.quantity',
                'products.price',
                'products.code',
                'products.made_in',
                'products.attributeCatalogue',
                'products.attribute',
                'products.variant',
                'products.qrcode',
                'products.name',
                'products.description',
                'products.content',
                'products.canonical',
                DB::raw('AVG(reviews.score) as avg_rating') // Tính điểm đánh giá trung bình
            ]
        )
            ->leftJoin('reviews', 'products.id', '=', 'reviews.product_id') // Đảm bảo kết nối với bảng reviews
            ->with([
                'product_catalogues',
                'product_variants' => function ($query) {
                    $query->with(['attributes']);
                },
                'reviews'
            ])
            ->where('products.publish', 2)
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.content',
                'products.canonical',
                'products.product_catalogue_id',
                'products.image',
                'products.album',
                'products.publish',
                'products.quantity',
                'products.price',
                'products.code',
                'products.made_in',
                'products.attributeCatalogue',
                'products.attribute',
                'products.variant',
                'products.qrcode',
            )
            ->orderByRaw('SUM(products.quantity) DESC, avg_rating DESC')
            ->limit(5)
            ->get();
    }

    public function getLatestProducts()
    {
        return $this->model->select([
            'products.id',
            'products.product_catalogue_id',
            'products.image',
            'products.album',
            'products.publish',
            'products.quantity',
            'products.price',
            'products.code',
            'products.made_in',
            'products.attributeCatalogue',
            'products.attribute',
            'products.variant',
            'products.qrcode',
            'products.name',
            'products.description',
            'products.content',
            'products.canonical',
            'products.created_at'
        ])
            ->where('products.publish', 2)
            ->whereNull('products.deleted_at')
            ->with([
                'product_catalogues',
                'product_variants' => function ($query) {
                    $query->with(['attributes']);
                }
            ])
            ->orderBy('products.created_at', 'DESC')
            ->limit(10)
            ->get();
    }

    public function getAllProducts()
    {
        return Product::select(
            'products.id as product_id',
            'products.code',
            'products.name as product_name',
        )->get();
    }

    public function getAllProductCatalogues()
    {
        return ProductCatalogue::select(
            'product_catalogues.id as catalogue_id',
            'product_catalogues.name as catalogue_name'
        )->get();
    }



    public function addProductCatalogueNamesToProducts($products)
    {
        $productIds = $products->pluck('id')->unique();
        $productCatalogueMapping = DB::table('product_catalogue_product')
            ->whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id');
        $catalogueIds = $productCatalogueMapping->flatMap(fn($items) => $items->pluck('product_catalogue_id'))->unique();
        $productCatalogues = DB::table('product_catalogues')
            ->whereIn('id', $catalogueIds)
            ->get(['id', 'name'])
            ->keyBy('id');
        $products->transform(function ($product) use ($productCatalogueMapping, $productCatalogues) {
            $catalogueIds = $productCatalogueMapping[$product->id] ?? collect();
            $product->array_product_catalogue_name = $catalogueIds->pluck('product_catalogue_id')->map(fn($id) => [
                'id' => $id,
                'name' => $productCatalogues[$id]->name ?? null
            ])->filter()->values();
            return $product;
        });
        return $products;
    }


    public function findProductForPromotion($condition = [], $relation = [])
    {
        $query = $this->model->newQuery();
        $query->select([
            'products.id',
            'products.image',
            'products.warranty',
            'products.name',
            'tb3.uuid',
            'tb3.id as product_variant_id',
            DB::raw('CONCAT(products.name, " - ", COALESCE(tb3.name, " Default")) as variant_name'),
            DB::raw('COALESCE(tb3.sku, products.code) as sku'),
            DB::raw('COALESCE(tb3.price, products.price) as price'),
        ]);
        $query->leftJoin('product_variants as tb3', 'products.id', '=', 'tb3.product_id');

        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }

        if (count($relation)) {
            $query->with($relation);
        }

        $query->orderBy('products.id', 'desc');
        $query->groupBy('products.id');

        return $query->paginate(20);
    }



    public function filter($param, $perpage, $orderBy)
    {
        $query = $this->model->newQuery();

        $query->select(
            'products.id',
            'products.price',
            'products.name',
            'products.image',
            'products.canonical'
        );

        if (isset($param['select']) && count($param['select'])) {
            foreach ($param['select'] as $key => $val) {
                if (is_null($val)) continue;
                $query->selectRaw($val);
            }
        }

        if (isset($param['join']) && count($param['join'])) {
            foreach ($param['join'] as $key => $val) {
                if (is_null($val)) continue;
                $query->leftJoin($val[0], $val[1], $val[2], $val[3]);
            }
        }

        $query->where('products.publish', '=', 2);

        if (isset($param['where']) && count($param['where'])) {
            foreach ($param['where'] as $key => $val) {
                $query->where($val);
            }
        }

        if (isset($param['whereRaw']) && count($param['whereRaw'])) {
            $query->whereRaw($param['whereRaw'][0][0], $param['whereRaw'][0][1]);
        }

        if (isset($param['having']) && count($param['having'])) {
            foreach ($param['having'] as $key => $val) {
                if (is_null($val)) continue;
                $query->having($val);
            }
        }

        $query->groupBy($orderBy);
        $query->with(['reviews', 'product_catalogues']);

        Log::info($query->toSql(), $query->getBindings());

        return $query->paginate($perpage);
    }

    public function breadcrumb($model, $language)
    {
        $breadcrumb = $this->findByCondition([
            ['lft', '<=', $model->lft],
            ['rgt', '>=', $model->rgt],
            ['publish', '=', 1],
        ], true, [], ['lft', 'asc']);
        return $breadcrumb;
    }

    public function updateProductTotalQuantity($products)
    {

        $productQuantities = DB::table('inventory_batches')
            ->select('product_id', DB::raw('SUM(quantity) as sum_quantity'))
            ->where('publish', 2)
            ->groupBy('product_id')
            ->pluck('sum_quantity', 'product_id');

        foreach ($products as &$product) {
            if (is_array($product)) {
                $product['total_quantity'] = isset($productQuantities[$product['id']]) ? $productQuantities[$product['id']] : 0;
            } elseif (is_object($product)) {
                $product->total_quantity = isset($productQuantities[$product->id]) ? $productQuantities[$product->id] : 0;
            }
        }
        unset($product);
        return $products;
    }

    public function widgetProductTotalQuantity($products)
    {
        $productQuantities = DB::table('inventory_batches')
            ->select('product_id', DB::raw('SUM(quantity) as sum_quantity'))
            ->where('publish', 2)
            ->groupBy('product_id')
            ->pluck('sum_quantity', 'product_id');

        foreach ($products as $product) {
            $product->total_quantity = isset($productQuantities[$product->id]) ? $productQuantities[$product->id] : 0;
        }
        return $products;
    }


    public function addProductCanonicalToReviews($reviews)
    {
        $productIds = $reviews->pluck('reviewable_id')->unique();
        $products = Product::whereIn('id', $productIds)->pluck('canonical', 'id');
        $reviews->transform(function ($review) use ($products) {
            $review->product_canonical = $products[$review->reviewable_id] ?? null;
            return $review;
        });

        return $reviews;
    }


    public function findByCondition(
        $condition = [],
        $flag = false,
        $relation = [],
        array $orderBy = ['id', 'desc'],
        array $param = [],
        array $withCount = [],
    ) {

        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        if (isset($param['whereIn'])) {
            $query->whereIn($param['whereInField'], $param['whereIn']);
        }

        $query->withCount($withCount);
        $query->orderBy($orderBy[0], $orderBy[1]);
        return ($flag == false) ? $query->first() : $query->get();
    }
}
