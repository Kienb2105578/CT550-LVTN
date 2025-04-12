<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Interfaces\OrderServiceInterface as OrderService;
use App\Services\Interfaces\CustomerServiceInterface as CustomerService;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    protected $orderService;
    protected $customerService;
    protected $orderRepository;

    public function __construct(
        OrderService $orderService,
        CustomerService $customerService,
        OrderRepository $orderRepository,
    ) {
        $this->orderService = $orderService;
        $this->customerService = $customerService;
        $this->orderRepository = $orderRepository;
    }

    public function time(Request $request)
    {
        $user = Auth::guard('web')->User();
        $orderStatistic = $this->orderService->statistic();
        $customerStatistic = $this->customerService->statistic();
        $reports = [];
        if ($request->input('startDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startDate)));
            $endDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endDate)));
            $reports = $this->orderRepository->getReportTime($startDate, $endDate);
        }

        $config = $this->config();
        $template = 'backend.report.time';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orderStatistic',
            'customerStatistic',
            'user',
            'reports'
        ));
    }

    public function product(Request $request)
    {
        $user = Auth::guard('web')->User();
        $orderStatistic = $this->orderService->statistic();
        $customerStatistic = $this->customerService->statistic();
        $reports = [];
        if ($request->input('startDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startDate)));
            $endDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endDate)));
            $reports = $this->orderRepository->getProductReportTime($startDate, $endDate);
        }
        $config = $this->config();
        $template = 'backend.report.product';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orderStatistic',
            'customerStatistic',
            'user',
            'reports'
        ));
    }
    public function exportFileProduct(Request $request)
    {
        $reports = [];
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        if (empty($startDate) || empty($endDate)) {
            toastr()->error('Dữ liệu không đầy đủ để xuất báo cáo!', 'Lỗi');
            return redirect()->back();
        }
        if ($request->input('startDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startDate)));
            $endDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endDate)));
            $reports = $this->orderRepository->getProductReportTime($startDate, $endDate);
        }
        Log::info('Xuất file báo cáo kho:', [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        $config = $this->config();
        $template = 'backend.report.exportFileProduct';
        $pdf = PDF::loadView('backend.report.exportFileProduct', compact(
            'reports',
            'template',
            'config'
        ));

        return $pdf->download('Bao_cao_' . now()->format('YmdHis') . '.pdf');
    }

    public function exportFileTime(Request $request)
    {
        $reports = [];
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        if (empty($startDate) || empty($endDate)) {
            toastr()->error('Dữ liệu không đầy đủ để xuất báo cáo!', 'Lỗi');
            return redirect()->back();
        }

        if ($request->input('startDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $startDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startDate)));
            $endDate = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $endDate)));
            $reports = $this->orderRepository->getReportTime($startDate, $endDate);
        }
        Log::info('Xuất file báo cáo kho:', [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        $config = $this->config();
        $template = 'backend.report.exportFileTime';
        $pdf = PDF::loadView('backend.report.exportFileTime', compact(
            'reports',
            'template',
            'config'
        ));

        return $pdf->download('Bao_cao_' . now()->format('YmdHis') . '.pdf');
    }

    private function config()
    {
        return [
            'js' => [
                'backend/js/plugins/chartJs/Chart.min.js',
                'backend/library/report.js',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.full.js',
            ],
            'css' => [
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.min.css',
                'backend/css/plugins/c3/c3.min.css',
            ]
        ];
    }
}
