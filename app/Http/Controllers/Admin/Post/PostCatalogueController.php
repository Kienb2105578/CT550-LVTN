<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostCatalogue;
use App\Models\Post;

use App\Services\Interfaces\PostCatalogueServiceInterface  as PostCatalogueService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface  as PostCatalogueRepository;
use App\Http\Requests\Post\StorePostCatalogueRequest;
use App\Http\Requests\Post\UpdatePostCatalogueRequest;
use App\Classes\Nestedsetbie;

class PostCatalogueController extends Controller
{

    protected $postCatalogueService;
    protected $postCatalogueRepository;
    protected $nestedset;

    public function __construct(
        PostCatalogueService $postCatalogueService,
        PostCatalogueRepository $postCatalogueRepository
    ) {
        $this->middleware(function ($request, $next) {
            $this->initialize();
            return $next($request);
        });


        $this->postCatalogueService = $postCatalogueService;
        $this->postCatalogueRepository = $postCatalogueRepository;
    }

    private function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
        ]);
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'post.catalogue.index');
        $postCatalogues = $this->postCatalogueService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'PostCatalogue',
        ];
        $config['seo'] = __('messages.postCatalogue');
        $template = 'admin.post.catalogue.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'postCatalogues'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'post.catalogue.create');
        $config = $this->configData();
        $config['seo'] = __('messages.postCatalogue');
        $config['method'] = 'create';
        $dropdown  = $this->nestedset->Dropdown();
        $template = 'admin.post.catalogue.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'dropdown',
            'config',
        ));
    }

    public function store(StorePostCatalogueRequest $request)
    {
        if ($this->postCatalogueService->create($request)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('post.catalogue.index')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'post.catalogue.update');
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id);
        $config = $this->configData();
        $config['seo'] = __('messages.postCatalogue');
        $config['method'] = 'edit';
        $dropdown  = $this->nestedset->Dropdown();
        $template = 'admin.post.catalogue.store';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'postCatalogue',
        ));
    }

    public function update($id, UpdatePostCatalogueRequest $request)
    {
        if ($this->postCatalogueService->update($id, $request)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('post.catalogue.index')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }


    public function destroy($id)
    {
        if (PostCatalogue::isNodeCheck($id)) {
            return redirect()->route('post.catalogue.index')->withErrors('Không thể xóa do vẫn còn danh mục con.');
        }
        if (Post::hasPosts($id)) {
            return redirect()->route('post.catalogue.index')->withErrors('Không thể xóa do danh mục này vẫn còn bài viết.');
        }
        if ($this->postCatalogueService->destroy($id)) {
            return redirect()->route('post.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }

        return redirect()->route('post.catalogue.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
