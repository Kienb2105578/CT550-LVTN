<?php

namespace App\Http\Controllers\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\ProductCatalogueServiceInterface  as ProductCatalogueService;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface  as ProductCatalogueRepository;
use App\Http\Requests\Product\StoreProductCatalogueRequest;
use App\Http\Requests\Product\UpdateProductCatalogueRequest;
use App\Classes\Nestedsetbie;
use App\Models\ProductCatalogue;
use App\Models\Product;

class ProductCatalogueController extends Controller
{

    protected $productCatalogueService;
    protected $productCatalogueRepository;
    protected $nestedset;

    public function __construct(
        ProductCatalogueService $productCatalogueService,
        ProductCatalogueRepository $productCatalogueRepository
    ) {
        $this->middleware(function ($request, $next) {
            $this->initialize();
            return $next($request);
        });


        $this->productCatalogueService = $productCatalogueService;
        $this->productCatalogueRepository = $productCatalogueRepository;
    }

    private function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
        ]);
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'product.catalogue.index');
        $productCatalogues = $this->productCatalogueService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'ProductCatalogue',
        ];
        $config['seo'] = __('messages.productCatalogue');
        $template = 'backend.product.catalogue.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'productCatalogues'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'product.catalogue.create');
        $config = $this->configData();
        $config['seo'] = __('messages.productCatalogue');
        $config['method'] = 'create';
        $dropdown  = $this->nestedset->Dropdown();
        $template = 'backend.product.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'dropdown',
            'config',
        ));
    }

    public function store(StoreProductCatalogueRequest $request)
    {
        if ($this->productCatalogueService->create($request)) {
            return redirect()->route('product.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id, Request $request)
    {
        $this->authorize('modules', 'product.catalogue.update');
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id);
        $queryUrl = $request->getQueryString();
        $config = $this->configData();
        $config['seo'] = __('messages.productCatalogue');
        $config['method'] = 'edit';
        $dropdown  = $this->nestedset->Dropdown();
        $template = 'backend.product.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'productCatalogue',
            'queryUrl'
        ));
    }

    public function update($id, UpdateProductCatalogueRequest $request)
    {
        $queryString = base64_decode($request->getQueryString());
        if ($this->productCatalogueService->update($id, $request)) {
            return redirect()->route('product.catalogue.index', $queryString)->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function destroy($id)
    {
        if (ProductCatalogue::isNodeCheck($id)) {
            return redirect()->route('product.catalogue.index')->with('error', 'Không thể xóa do vẫn còn danh mục con.');
        }
        if (Product::hasProducts($id)) {
            return redirect()->route('product.catalogue.index')->with('error', 'Không thể xóa do danh mục này vẫn sản phẩm.');
        }
        if ($this->productCatalogueService->destroy($id)) {
            return redirect()->route('product.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData()
    {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]

        ];
    }
}
