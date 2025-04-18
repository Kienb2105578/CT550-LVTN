<?php

namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\AttributeServiceInterface  as AttributeService;
use App\Services\Interfaces\AttributeCatalogueServiceInterface  as AttributeCatalogueService;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface  as AttributeCatalogueRepository;
use App\Http\Requests\Attribute\StoreAttributeCatalogueRequest;
use App\Http\Requests\Attribute\UpdateAttributeCatalogueRequest;

class AttributeCatalogueController extends Controller
{

    protected $attributeCatalogueService;
    protected $attributeCatalogueRepository;
    protected $nestedset;

    public function __construct(
        AttributeCatalogueService $attributeCatalogueService,
        AttributeCatalogueRepository $attributeCatalogueRepository
    ) {
        $this->middleware(function ($request, $next) {
            return $next($request);
        });


        $this->attributeCatalogueService = $attributeCatalogueService;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'attribute.catalogue.index');
        $attributeCatalogues = $this->attributeCatalogueService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'AttributeCatalogue',
        ];
        $config['seo'] = __('messages.attributeCatalogue');
        $template = 'admin.attribute.catalogue.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'attributeCatalogues'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'attribute.catalogue.create');
        $config = $this->configData();
        $config['seo'] = __('messages.attributeCatalogue');
        $config['method'] = 'create';
        $template = 'admin.attribute.catalogue.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Attribute\StoreAttributeCatalogueRequest $request
     * attribute.catalogue.index
     */

    public function store(StoreAttributeCatalogueRequest $request)
    {
        if ($this->attributeCatalogueService->create($request)) {
            return redirect()->route('attribute.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id, Request $request)
    {
        $this->authorize('modules', 'attribute.catalogue.update');
        $attributeCatalogue = $this->attributeCatalogueRepository->getAttributeCatalogueById($id);
        $queryUrl = $request->getQueryString();
        $config = $this->configData();
        $config['seo'] = __('messages.attributeCatalogue');
        $config['method'] = 'edit';
        $template = 'admin.attribute.catalogue.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'attributeCatalogue',
            'queryUrl'
        ));
    }

    public function update($id, UpdateAttributeCatalogueRequest $request)
    {
        $queryUrl = base64_decode($request->getQueryString());
        if ($this->attributeCatalogueService->update($id, $request)) {
            return redirect()->route('attribute.index', $queryUrl)->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'attribute.catalogue.destroy');
        $config['seo'] = __('messages.attributeCatalogue');
        $attributeCatalogue = $this->attributeCatalogueRepository->getAttributeCatalogueById($id);
        $template = 'admin.attribute.catalogue.delete';
        return view('admin.dashboard.layout', compact(
            'template',
            'attributeCatalogue',
            'config',
        ));
    }

    public function destroy($id)
    {
        if ($this->attributeCatalogueService->destroy($id)) {
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
