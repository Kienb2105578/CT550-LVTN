<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\SlideServiceInterface  as SlideService;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Slide\StoreSlideRequest;
use App\Http\Requests\Slide\UpdateSlideRequest;

class SlideController extends Controller
{
    protected $slideService;
    protected $slideRepository;

    public function __construct(
        SlideService $slideService,
        SlideRepository $slideRepository,
    ) {
        $this->slideService = $slideService;
        $this->slideRepository = $slideRepository;
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'slide.index');
        $slides = $this->slideService->paginate($request);

        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/slide.js',
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Slide'
        ];
        $config['seo'] = __('messages.slide');
        $template = 'admin.slide.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'slides'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'slide.create');
        $config = $this->config();
        $config['seo'] = __('messages.slide');
        $config['method'] = 'create';
        $template = 'admin.slide.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSlideRequest $request)
    {
        if ($this->slideService->create($request)) {
            return redirect()->route('slide.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'slide.edit');
        $slide = $this->slideRepository->findById($id);
        $slideItem = $this->slideService->converSlideArray($slide->item);
        $config = $this->config();
        $config['seo'] = __('messages.slide');
        $config['method'] = 'edit';
        $template = 'admin.slide.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'slide',
            'slideItem',
        ));
    }

    public function update($id, UpdateSlideRequest $request)
    {
        if ($this->slideService->update($id, $request)) {
            return redirect()->route('slide.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }



    public function destroy($id)
    {
        if ($this->slideService->destroy($id)) {
            return redirect()->route('slide.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
                'backend/library/slide.js',
            ]
        ];
    }
}
