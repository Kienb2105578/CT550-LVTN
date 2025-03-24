<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\OrderServiceInterface  as OrderService;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;

use Barryvdh\DomPDF\Facade\Pdf as PDF;


class OrderController extends Controller
{
    protected $orderService;
    protected $orderRepository;

    public function __construct(
        OrderService $orderService,
        OrderRepository $orderRepository,
        ProvinceRepository $provinceRepository,
    ) {
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->provinceRepository = $provinceRepository;
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
        $order = $this->orderRepository->getOrderById($id, ['products']);
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

    public function exportPdf($id)
    {
        $order = $this->orderRepository->getOrderById($id, ['products']);
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

        // return $pdf->download("invoice_{$id}.pdf");
        return $pdf->stream("invoice_{$id}.pdf");
    }

    public function exportMultiplePdf(Request $request)
    {
        $orderIds = $request->input('order_ids');
        $orders = $this->orderRepository->getOrdersByIds($orderIds, ['products']);

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
}
