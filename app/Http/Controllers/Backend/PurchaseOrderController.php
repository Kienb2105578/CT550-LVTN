<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\PurchaseOrderServiceInterface  as PurchaseOrderService;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface  as PurchaseOrderRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductOrderRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface  as AttributeRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface  as AttributeCatalogueRepository;
use App\Repositories\Interfaces\SupplierRepositoryInterface  as SupplierRepository;
use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\UpdatePurchaseOrderRequest;
use App\Classes\Nestedsetbie;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    protected $purchaseOrderService;
    protected $purchaseOrderRepository;
    protected $languageRepository;
    protected $language;
    protected $attributeCatalogue;
    protected $supplierRepository;
    protected $attributeRepository;
    protected $productRepository;

    public function __construct(
        PurchaseOrderService $purchaseOrderService,
        PurchaseOrderRepository $purchaseOrderRepository,
        AttributeCatalogueRepository $attributeCatalogue,
        AttributeRepository $attributeRepository,
        ProductOrderRepository $productRepository,
        SupplierRepository $supplierRepository,
    ) {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });

        $this->purchaseOrderService = $purchaseOrderService;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->attributeCatalogue = $attributeCatalogue;
        $this->attributeRepository = $attributeRepository;
        $this->productRepository = $productRepository;
        $this->supplierRepository = $supplierRepository;
    }


    public function index(Request $request)
    {
        $this->authorize('modules', 'purchase-order.index');
        $purchaseOrders = $this->purchaseOrderService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'PurchaseOrder'
        ];
        $config['seo'] = __('messages.purchase-order');
        $template = 'backend.purchase-order.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'purchaseOrders'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'purchase-order.create');
        $products = $this->productRepository->getAllProducts();
        $suppliers = $this->supplierRepository->getAllSuppliers();
        $config = $this->configData();
        $config['seo'] = __('messages.purchase-order');
        $config['method'] = 'create';
        $template = 'backend.purchase-order.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'products',
            'suppliers',
        ));
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        if ($this->purchaseOrderService->create($request, $this->language)) {
            return redirect()->route('purchase-order.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('purchase-order.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id, Request $request)
    {
        $this->authorize('modules', 'purchase-order.update');
        $purchaseOrder = $this->purchaseOrderRepository->getPurchaseOrderById($id, $this->language);
        $products = $this->productRepository->getAllProducts();
        $suppliers = $this->supplierRepository->getAllSuppliers();

        $queryUrl = $request->getQueryString();
        $config = $this->configData();
        $config['seo'] = __('messages.purchase-order');
        $config['method'] = 'edit';

        $template = 'backend.purchase-order.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'purchaseOrder',
            'queryUrl',
            'products',
            'suppliers'
        ));
    }

    public function update($id, UpdatePurchaseOrderRequest $request)
    {
        $queryUrl = base64_decode($request->getQueryString());
        if ($this->purchaseOrderService->update($id, $request)) {
            return redirect()->route('purchase-order.index', $queryUrl)->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('purchase-order.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function destroy($id)
    {
        if ($this->purchaseOrderService->destroy($id, $this->language)) {
            return redirect()->route('purchase-order.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('purchase-order.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData()
    {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'backend/library/purchase.js',
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/nice-select/js/jquery.nice-select.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugins/nice-select/css/nice-select.css',
                'backend/css/plugins/switchery/switchery.css',
            ]

        ];
    }
}
