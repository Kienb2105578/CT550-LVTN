<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\CustomerServiceInterface  as CustomerService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface  as ProvinceRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;

class CustomerController extends Controller
{
    protected $customerService;
    protected $provinceRepository;
    protected $customerRepository;


    public function __construct(
        CustomerService $customerService,
        ProvinceRepository $provinceRepository,
        CustomerRepository $customerRepository,
    ) {
        $this->customerService = $customerService;
        $this->provinceRepository = $provinceRepository;
        $this->customerRepository = $customerRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'customer.index');
        $customers = $this->customerService->paginate($request);

        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Customer'
        ];
        $config['seo'] = __('messages.customer');
        $template = 'admin.customer.customer.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'customers',
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'customer.create');
        $provinces = $this->provinceRepository->all();
        $config = $this->config();
        $config['seo'] = __('messages.customer');
        $config['method'] = 'create';
        $template = 'admin.customer.customer.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
        ));
    }

    public function store(StoreCustomerRequest $request)
    {
        if ($this->customerService->create($request)) {
            return redirect()->route('customer.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'customer.update');
        $customer = $this->customerRepository->findById($id);
        $provinces = $this->provinceRepository->all();
        $config = $this->config();
        $config['seo'] = __('messages.customer');
        $config['method'] = 'edit';
        $template = 'admin.customer.customer.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'customer',
        ));
    }

    public function update($id, UpdateCustomerRequest $request)
    {
        if ($this->customerService->update($id, $request)) {
            return redirect()->route('customer.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function destroy($id)
    {
        if ($this->customerService->destroy($id)) {
            return redirect()->route('customer.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('customer.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
