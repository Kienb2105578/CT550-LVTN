<?php

namespace App\Http\Controllers\Backend\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\SupplierServiceInterface  as SupplierService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface  as ProvinceRepository;
use App\Repositories\Interfaces\SupplierRepositoryInterface as SupplierRepository;

use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;

class SupplierController extends Controller
{
    protected $supplierService;
    protected $provinceRepository;
    protected $supplierRepository;
    protected $supplierCatalogueRepository;

    public function __construct(
        SupplierService $supplierService,
        ProvinceRepository $provinceRepository,
        SupplierRepository $supplierRepository,
    ) {
        $this->supplierService = $supplierService;
        $this->provinceRepository = $provinceRepository;
        $this->supplierRepository = $supplierRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'supplier.index');
        $suppliers = $this->supplierService->paginate($request);

        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Supplier'
        ];
        $config['seo'] = __('messages.supplier');
        $template = 'backend.supplier.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'suppliers',
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'supplier.create');
        $provinces = $this->provinceRepository->all();
        $config = $this->config();
        $config['seo'] = __('messages.supplier');
        $config['method'] = 'create';
        $template = 'backend.supplier.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
        ));
    }

    public function store(StoreSupplierRequest $request)
    {
        if ($this->supplierService->create($request)) {
            return redirect()->route('supplier.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('supplier.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'supplier.update');
        $supplier = $this->supplierRepository->findById($id);
        $provinces = $this->provinceRepository->all();
        $config = $this->config();
        $config['seo'] = __('messages.supplier');
        $config['method'] = 'edit';
        $template = 'backend.supplier.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'supplier',
        ));
    }

    public function update($id, UpdateSupplierRequest $request)
    {
        if ($this->supplierService->update($id, $request)) {
            return redirect()->route('supplier.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('supplier.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }


    public function destroy($id)
    {
        if ($this->supplierService->destroy($id)) {
            return redirect()->route('supplier.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('supplier.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function config()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',

            ]
        ];
    }
}
