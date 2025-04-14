<?php

namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\AttributeServiceInterface  as AttributeService;
use App\Repositories\Interfaces\AttributeRepositoryInterface  as AttributeRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface  as AttributeCatalogueRepository;
use App\Http\Requests\Attribute\StoreAttributeRequest;
use App\Http\Requests\Attribute\UpdateAttributeRequest;


class AttributeController extends Controller
{
    protected $attributeService;
    protected $attributeRepository;
    protected $attributeCatalogueRepository;

    public function __construct(
        AttributeService $attributeService,
        AttributeRepository $attributeRepository,
        AttributeCatalogueRepository $attributeCatalogueRepository,
    ) {
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->attributeService = $attributeService;
        $this->attributeRepository = $attributeRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'attribute.index');
        $attributes = $this->attributeService->paginate($request);
        $attributes = $this->attributeRepository->addAttributeCatalogueNamesToAttributes($attributes);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Attribute'
        ];
        $config['seo'] = __('messages.attribute');
        $template = 'admin.attribute.attribute.index';
        $dropdown  = $this->attributeCatalogueRepository->getAll();
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'attributes'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'attribute.create');
        $config = $this->configData();
        $config['seo'] = __('messages.attribute');
        $config['method'] = 'create';
        $dropdown  = $this->attributeCatalogueRepository->getAll();
        $template = 'admin.attribute.attribute.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'dropdown',
            'config',
        ));
    }

    public function store(StoreAttributeRequest $request)
    {
        if ($this->attributeService->create($request)) {
            return redirect()->route('attribute.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id, Request $request)
    {
        $this->authorize('modules', 'attribute.update');
        $attribute = $this->attributeRepository->getAttributeById($id);
        $queryUrl = $request->getQueryString();
        $config = $this->configData();
        $config['seo'] = __('messages.attribute');
        $config['method'] = 'edit';
        $dropdown  = $this->attributeCatalogueRepository->getAll();
        $album = json_decode($attribute->album);
        $template = 'admin.attribute.attribute.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'attribute',
            'album',
            'queryUrl'
        ));
    }

    public function update($id, UpdateAttributeRequest $request)
    {
        $queryUrl = base64_decode($request->getQueryString());
        if ($this->attributeService->update($id, $request)) {
            return redirect()->route('attribute.index', $queryUrl)->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'attribute.destroy');
        $config['seo'] = __('messages.attribute');
        $attribute = $this->attributeRepository->getAttributeById($id);
        $template = 'admin.attribute.attribute.delete';
        return view('admin.dashboard.layout', compact(
            'template',
            'attribute',
            'config',
        ));
    }

    public function destroy($id)
    {
        if ($this->attributeService->destroy($id)) {
            return redirect()->route('attribute.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData()
    {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]

        ];
    }
}
