<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Services\Interfaces\PostCatalogueServiceInterface as PostCatalogueService;
use App\Services\Interfaces\PostServiceInterface as PostService;


class PostCatalogueController extends FrontendController
{
    protected $system;
    protected $postCatalogueRepository;
    protected $postCatalogueService;
    protected $postService;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostCatalogueService $postCatalogueService,
        PostService $postService,
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postCatalogueService = $postCatalogueService;
        $this->postService = $postService;
        parent::__construct();
    }


    public function index($id, $request, $page = 1)
    {
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id);
        $breadcrumb = $this->postCatalogueRepository->breadcrumb($postCatalogue, 1);
        $posts = $this->postService->paginate(
            $request,
            $postCatalogue,
            $page,
            ['path' => $postCatalogue->canonical],
        );

        $template = 'frontend.post.catalogue.index';
        $system = $this->system;
        $seo = seo($postCatalogue, $page);
        return view($template, compact(
            'seo',
            'system',
            'breadcrumb',
            'postCatalogue',
            'posts',
        ));
    }
}
