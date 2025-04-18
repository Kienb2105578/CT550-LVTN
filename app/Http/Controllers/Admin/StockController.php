<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\InventoryBatchServiceInterface  as InventoryBatchService;
use App\Services\Interfaces\StockMovementServiceInterface  as StockMovementService;
use App\Services\Interfaces\StockTakingServiceInterface  as StockTakingService;
use App\Repositories\Interfaces\InventoryBatchRepositoryInterface  as InventoryBatchRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;
use App\Http\Requests\Stock\StoreStockTakingRequest;
use App\Http\Requests\Stock\UpdateStockTakingRequest;
use Illuminate\Support\Facades\Log;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

class StockController extends Controller
{
    protected $inventoryBatchRepository;
    protected $inventoryBatchService;
    protected $stockMovementService;
    protected $stockTakingService;
    protected $productRepository;

    public function __construct(
        StockMovementService $stockMovementService,
        StockTakingService $stockTakingService,
        InventoryBatchService $inventoryBatchService,
        InventoryBatchRepository $inventoryBatchRepository,
        ProductRepository $productRepository,
    ) {
        $this->inventoryBatchService = $inventoryBatchService;
        $this->stockMovementService = $stockMovementService;
        $this->inventoryBatchRepository = $inventoryBatchRepository;
        $this->productRepository = $productRepository;
        $this->stockTakingService = $stockTakingService;
    }


    public function index(Request $request)
    {
        $this->authorize('modules', 'purchase-order.index');

        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Stock'
        ];
        $config['seo'] = __('messages.purchase-order');
        $template = 'admin.purchase-order.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',

        ));
    }
    public function report(Request $request)
    {
        $this->authorize('modules', 'stock.report.index');
        $products = $this->inventoryBatchRepository->getInventoryWithProduct();
        $catalogues = $this->productRepository->getAllProductCatalogues();
        $config = array_merge($this->configData(), [
            'seo' => __('messages.stock')
        ]);
        $template = 'admin.stock.report.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'products',
            'catalogues'
        ));
    }


    /**
     * 
     * DANH SÁCH CÁC BIẾN ĐỘNG TRONG KHO
     * 
     */
    public function stockTaking(Request $request)
    {
        $this->authorize('modules', 'stock.stock-taking.index');
        $stocks = $this->stockMovementService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
        $config['seo'] = __('messages.stock');
        $template = 'admin.stock.stock-taking.index';

        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'stocks'
        ));
    }
    public function createStockTaking()
    {
        $this->authorize('modules', 'stock.stock-taking.create');
        $codes = $this->inventoryBatchRepository->getCodeInventory();
        $config = $this->config();
        $config['seo'] = __('messages.stock');
        $config['method'] = 'create';
        $template = 'admin.stock.stock-taking.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'codes',
        ));
    }

    public function storecreateStockTaking(StoreStockTakingRequest $request)
    {
        if ($this->stockTakingService->create($request)) {
            return redirect()->route('stock.stock-taking.list')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('stock.stock-taking.list')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    /**
     * Summary of listStockTaking
     * DANH SÁCH CÁC PHIẾU KIỂM KÊ KHO
     * 
     */
    public function listStockTaking(Request $request)
    {
        $this->authorize('modules', 'stock.stock-taking.list');
        $stocks = $this->stockTakingService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
        $config['seo'] = __('messages.stock');
        $template = 'admin.stock.stock-taking.list';

        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'stocks'
        ));
    }
    public function destroyStockTaking($id)
    {
        if ($this->stockTakingService->destroy($id)) {
            return redirect()->route('stock.stock-taking.list')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('stock.stock-taking.list')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function updateStockTaking($id, UpdateStockTakingRequest $request)
    {
        if ($this->stockTakingService->update($id, $request)) {
            return redirect()->route('stock.stock-taking.list')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('stock.stock-taking.list')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }


    public function inventory(Request $request)
    {
        $this->authorize('modules', 'stock.inventory.index');
        $inventorys = $this->inventoryBatchService->paginate($request);
        $products = $this->productRepository->getAllProducts();
        $config = array_merge($this->configData(), [
            'model' => 'InventoryBatch',
            'seo' => __('messages.stock')
        ]);
        $template = 'admin.stock.inventory.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'inventorys',
            'products',
        ));
    }
    public function exportFile(Request $request)
    {
        $catalogue_id = $request->input('catalogue_id');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');


        if (empty($catalogue_id) || empty($startDate) || empty($endDate)) {
            toastr()->error('Dữ liệu không đầy đủ để xuất báo cáo!', 'Lỗi')->timeout(3000);
            return redirect()->back();
        }


        Log::info('Xuất file báo cáo kho:', [
            'catalogue_id' => $catalogue_id,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        $products = $this->inventoryBatchRepository->getReport($catalogue_id, $startDate, $endDate);

        $config['seo'] = __('messages.stock');
        $template = 'admin.stock.report.exportFile';

        $pdf = PDF::loadView('admin.stock.report.exportFile', compact(
            'products',
            'template',
            'config'
        ));

        return $pdf->download('Bao_cao_kho_' . now()->format('YmdHis') . '.pdf');
    }
    private function config()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
            ]
        ];
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
                'backend/library/stock.js',
                'backend/js/plugins/switchery/switchery.js',
                'backend/js/plugins/d3/d3.min.js',
                'backend/js/plugins/c3/c3.min.js',
                'backend/library/report.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/nice-select/js/jquery.nice-select.min.js',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.full.js',
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugins/nice-select/css/nice-select.css',
                'backend/css/plugins/switchery/switchery.css',
                'backend/css/plugins/c3/c3.min.css',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.min.css',
            ]

        ];
    }
}
