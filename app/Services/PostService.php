<?php

namespace App\Services;

use App\Services\Interfaces\PostServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


/**
 * Class PostService
 * @package App\Services
 */
class PostService extends BaseService implements PostServiceInterface
{
    protected $postRepository;
    protected $routerRepository;

    public function __construct(
        PostRepository $postRepository,
        RouterRepository $routerRepository,
    ) {
        $this->postRepository = $postRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'PostController';
    }

    private function whereRaw($request, $postCatalogue)
    {
        $rawCondition = [];
        if ($request->integer('post_catalogue_id') > 0 || !is_null($postCatalogue)) {
            $catId = ($request->integer('post_catalogue_id') > 0)
                ? $request->integer('post_catalogue_id')
                : ($postCatalogue?->id ?? 0);
            $rawCondition['whereRaw'] = [
                [
                    'tb3.post_catalogue_id IN (
                    SELECT id
                    FROM post_catalogues
                    WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
                    AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id = ?)
                )',
                    [$catId, $catId]
                ]
            ];
        }
        return $rawCondition;
    }


    public function paginate($request = null, $postCatalogue = null, $page = 1, $extend = [])
    {
        $perPage = (!is_null($postCatalogue))  ? 15 : 20;
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
        ];

        $paginationConfig = [
            'path' => ($extend['path']) ?? 'post/index',
            'groupBy' => $this->paginateSelect()
        ];


        $orderBy = ['posts.id', 'DESC'];
        $relations = ['post_catalogues'];
        $rawQuery = $this->whereRaw($request, $postCatalogue);

        $joins = [
            ['post_catalogue_post as tb3', 'posts.id', '=', 'tb3.post_id'],
        ];

        $posts = $this->postRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            $paginationConfig,
            $orderBy,
            $joins,
            $relations,
            $rawQuery
        );
        return $posts;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $post = $this->createPost($request);
            if ($post->id > 0) {
                $this->updateCatalogueForPost($post, $request);
                $this->createRouter($post, $request, $this->controllerName);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->findById($id);
            if ($this->uploadPost($post, $request)) {
                $this->updateCatalogueForPost($post, $request);
                $this->updateRouter(
                    $post,
                    $request,
                    $this->controllerName,
                );
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->delete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\PostController'],
            ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    private function createPost($request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $post = $this->postRepository->create($payload);
        return $post;
    }

    private function uploadPost($post, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->postRepository->update($post->id, $payload);
    }


    private function updateCatalogueForPost($post, $request)
    {
        $post->post_catalogues()->sync($this->catalogue($request));
    }

    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->post_catalogue_id]));
        }
        return [$request->post_catalogue_id];
    }



    private function paginateSelect()
    {
        return [
            'posts.id',
            'posts.publish',
            'posts.image',
            'posts.order',
            'posts.name',
            'posts.description',
            'posts.content',
            'posts.meta_title',
            'posts.meta_keyword',
            'posts.meta_description',
            'posts.canonical'
        ];
    }

    private function payload()
    {
        return [
            'follow',
            'publish',
            'image',
            'album',
            'post_catalogue_id',
            'video',
            'name',
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical'
        ];
    }
}
