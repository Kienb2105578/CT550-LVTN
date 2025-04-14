<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\System;
use App\Services\Interfaces\SystemServiceInterface  as SystemService;
use App\Repositories\Interfaces\SystemRepositoryInterface  as SystemRepository;
use App\Models\Language;

class SystemController extends Controller
{
    protected $systemLibrary;
    protected $systemService;
    protected $systemRepository;

    public function __construct(
        System $systemLibrary,
        SystemService $systemService,
        SystemRepository $systemRepository,

    ) {
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
        $this->systemLibrary = $systemLibrary;
        $this->systemService = $systemService;
        $this->systemRepository = $systemRepository;
    }

    public function index()
    {

        $systemConfig = $this->systemLibrary->config();
        $systems = convert_array($this->systemRepository->all(), 'keyword', 'content');

        $config = $this->config();
        $config['seo'] = __('messages.system');
        $template = 'admin.system.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'systemConfig',
            'systems',
        ));
    }

    public function store(Request $request)
    {
        if ($this->systemService->save($request)) {
            return redirect()->route('system.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('system.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }


    private function config()
    {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
            ]
        ];
    }
}
