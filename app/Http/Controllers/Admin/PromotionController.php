<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\PromotionServiceInterface  as PromotionService;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;

use App\Http\Requests\Promotion\StorePromotionRequest;
use App\Http\Requests\Promotion\UpdatePromotionRequest;


class PromotionController extends Controller
{
    protected $promotionService;
    protected $promotionRepository;

    public function __construct(
        PromotionService $promotionService,
        PromotionRepository $promotionRepository,
    ) {
        $this->promotionService = $promotionService;
        $this->promotionRepository = $promotionRepository;
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'promotion.index');
        $promotions = $this->promotionService->paginate($request);

        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Promotion'
        ];
        $config['seo'] = __('messages.promotion');
        $template = 'admin.promotion.promotion.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'promotions'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'promotion.create');
        $config = $this->config();
        $config['seo'] = __('messages.promotion');
        $config['method'] = 'create';
        $template = 'admin.promotion.promotion.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StorePromotionRequest $request)
    {
        if ($this->promotionService->create($request)) {
            return redirect()->route('promotion.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }



    public function edit($id)
    {
        $this->authorize('modules', 'promotion.update');
        $promotion = $this->promotionRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.promotion');
        $config['method'] = 'edit';
        $template = 'admin.promotion.promotion.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'promotion',
        ));
    }

    public function update($id, UpdatePromotionRequest $request)
    {
        if ($this->promotionService->update($id, $request)) {
            return redirect()->route('promotion.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }


    public function destroy($id)
    {
        if ($this->promotionService->destroy($id)) {
            return redirect()->route('promotion.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }


    private function config()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.full.js',
                'backend/library/promotion.js',
            ]
        ];
    }
}
