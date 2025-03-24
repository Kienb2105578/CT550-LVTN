<?php

namespace App\Http\Controllers\Frontend\MyOrder;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Customer\EditProfileRequest;
use App\Http\Requests\Customer\RecoverCustomerPasswordRequest;
use App\Services\Interfaces\CustomerServiceInterface  as CustomerService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface  as ProvinceRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface  as OrderRepository;
use App\Services\Interfaces\OrderServiceInterface  as OrderService;
use Gloudemans\Shoppingcart\Facades\Cart;

class MyOrderController extends FrontendController
{
    protected $customerService;
    protected $constructRepository;
    protected $constructService;
    protected $customer;
    protected $provinceRepository;
    protected $orderRepository;
    protected $orderService;

    public function __construct(
        CustomerService $customerService,
        ProvinceRepository $provinceRepository,
        OrderRepository $orderRepository,
        OrderService $orderService

    ) {

        $this->customerService = $customerService;
        $this->provinceRepository = $provinceRepository;
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
        parent::__construct();
    }
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $orders = $this->orderRepository->getOrdersByCustomer($customer->id);
        $system = $this->system;
        $config = $this->config();
        $seo = [
            'meta_title' => 'Trang quản lý tài khoản khách hàng' . $customer['name'],
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('my-order.index')
        ];
        return view('frontend.order.my-order', compact(
            'seo',
            'system',
            'customer',
            'config',
            'orders'
        ));
    }


    public function detail($id)
    {
        $customer = Auth::guard('customer')->user();
        $order = $this->orderRepository->getOrderById($id);
        $order = $this->orderService->getOrderItemImage($order);
        $provinces = $this->provinceRepository->all();
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'frontend/core/library/order.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
        ];
        $seo = [
            'meta_title' => 'Chi tiết đơn hàng ' . $order->id . ' của khách hàng ' . $customer['name'],
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('my-order.detail', ['id' => $order->id])
        ];


        $system = $this->system;
        return view('frontend.order.detail', compact(
            'seo',
            'system',
            'config',
            'order',
            'provinces',
        ));
    }
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        // Kiểm tra trạng thái đơn hàng trước khi hủy
        if ($order->confirm !== 'pending') {
            return redirect()->route('my-order.index')->with('error', 'Không thể hủy đơn hàng này.');
        }

        // Cập nhật trạng thái đơn hàng
        $this->orderService->updateCancel($id);

        return redirect()->route('my-order.index')->with('success', 'Đơn hàng đã được hủy thành công.');
    }


    private function config()
    {
        return [
            'language' => $this->language,
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'frontend/core/library/order.js',
                'frontend/core/library/cart.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ]
        ];
    }
}
