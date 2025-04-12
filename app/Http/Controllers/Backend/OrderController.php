<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\OrderServiceInterface  as OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;
use App\Http\Requests\StoreOrderRequest;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


class OrderController extends Controller
{
    protected $orderService;
    protected $orderRepository;
    protected $provinceRepository;
    protected $productRepository;

    public function __construct(
        OrderService $orderService,
        OrderRepository $orderRepository,
        ProvinceRepository $provinceRepository,
        ProductRepository $productRepository,
    ) {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->provinceRepository = $provinceRepository;
        $this->productRepository = $productRepository;
    }

    public function index(Request $request)
    {

        $this->authorize('modules', 'order.index');
        $orders = $this->orderService->paginate($request);
        $config = [
            'js' => [
                'backend/library/order.js',
                'backend/js/plugins/switchery/switchery.js',
                'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
                'backend/js/plugins/daterangepicker/daterangepicker.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'backend/css/plugins/daterangepicker/daterangepicker-bs3.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Order'
        ];
        $config['seo'] = __('messages.order');
        $template = 'backend.order.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orders'
        ));
    }

    public function detail(Request $request, $id)
    {
        $order = $this->orderRepository->getOrderById($id);
        $order = $this->orderService->getOrderItemImage($order);
        $provinces = $this->provinceRepository->all();
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/library/order.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
        ];

        $config['seo'] = __('messages.order');
        $template = 'backend.order.detail';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'order',
            'provinces',
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'order.create');
        $provinces = $this->provinceRepository->all();
        $products = $this->productRepository->getAllProducts();
        $config = $this->config();
        $config['seo'] = __('messages.order');
        $config['method'] = 'create';
        $template = 'backend.order.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'products',
        ));
    }

    public function store(StoreOrderRequest $request)
    {
        if ($this->orderService->create($request)) {
            return redirect()->route('order.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('order.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function exportPdf($id)
    {
        $order = $this->orderRepository->getOrderById($id);
        $order = $this->orderService->getOrderItemImage($order);
        $provinces = $this->provinceRepository->all();

        $config['seo'] = __('messages.order');
        $template = 'backend.orders.invoice';

        $pdf = PDF::loadView('backend.order.invoice', compact(
            'order',
            'provinces',
            'template',
            'config'
        ));
        return $pdf->stream("invoice_{$id}.pdf");
    }

    /**
     * 
     * Chưa Dùng
     * 
     */

    public function exportMultiplePdf(Request $request)
    {
        $orderIds = $request->input('order_ids');
        $orders = $this->orderRepository->getOrderById($orderIds);

        foreach ($orders as $order) {
            $order = $this->orderService->getOrderItemImage($order);
        }

        $provinces = $this->provinceRepository->all();
        $config['seo'] = __('messages.orders');
        $template = 'backend.orders.multiple_invoices';

        $pdf = PDF::loadView('backend.order.multiple_invoices', compact(
            'orders',
            'provinces',
            'template',
            'config'
        ));

        return $pdf->download('multiple_invoices.pdf');
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
                'backend/library/order.js',
            ]
        ];
    }
}
