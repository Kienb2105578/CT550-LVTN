<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Services\Interfaces\PostCatalogueServiceInterface as PostCatalogueService;
use App\Services\Interfaces\PostServiceInterface as PostService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Models\System;
use Gloudemans\Shoppingcart\Facades\Cart;

class PostController extends FrontendController
{
    protected $language;
    protected $system;
    protected $postCatalogueRepository;
    protected $postCatalogueService;
    protected $postService;
    protected $postRepository;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostCatalogueService $postCatalogueService,
        PostService $postService,
        PostRepository $postRepository,
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postCatalogueService = $postCatalogueService;
        $this->postService = $postService;
        $this->postRepository = $postRepository;
        parent::__construct();
    }
    public function main($id, $page = 1)
    {
        $posts = $this->postRepository->getAllPosts();
        $system = $this->system;
        $seo = [
            'meta_title' => 'Bài Viết',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('post.main')
        ];
        return view('frontend.post.post.main', compact(
            'seo',
            'system',
            'posts',
        ));
    }

    public function index($id, $request)
    {
        $post = $this->postRepository->getPostById($id);
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($post->post_catalogue_id);
        $breadcrumb = $this->postCatalogueRepository->breadcrumb($postCatalogue, 1);

        $asidePost = $this->postService->paginate(
            $request,
            $postCatalogue,
            1,
            ['path' => $postCatalogue->canonical],
        );
        $system = $this->system;
        $seo = seo($post);
        return view('frontend.post.post.index', compact(
            'seo',
            'system',
            'breadcrumb',
            'postCatalogue',
            'post',
            'asidePost',
        ));
    }
}
