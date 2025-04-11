<?php

namespace App\Services;

use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;

/**
 * Class PostCatalogueService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{


    protected $postCatalogueRepository;
    protected $routerRepository;
    protected $postRepository;
    protected $nestedset;
    protected $language;
    protected $controllerName = 'PostCatalogueController';


    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        RouterRepository $routerRepository,
        PostRepository $postRepository,
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->routerRepository = $routerRepository;
        $this->postRepository = $postRepository;
        // $this->nestedset = new Nestedsetbie([
        //     'table' => 'post_catalogues',
        //     'foreignkey' => 'post_catalogue_id',
        //     'language_id' =>   ,
        // ]);
    }


    public function paginate($request, $languageId)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),

        ];
        $postCatalogues = $this->postCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'post.catalogue.index'],
            ['post_catalogues.lft', 'ASC'],

        );

        return $postCatalogues;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $postCatalogue = $this->createCatalogue($request);
            if ($postCatalogue->id > 0) {
                //$this->updateLanguageForCatalogue($postCatalogue, $request, $languageId);
                $this->createRouter($postCatalogue, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
                    'language_id' => $languageId,
                ]);
                $this->nestedset->Get();
                $arrSet = $this->nestedset->Set();
                $this->nestedset->Recursive(0, $arrSet);
                $this->nestedset->Action();
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
            $postCatalogue = $this->postCatalogueRepository->findById($id);
            $flag = $this->updateCatalogue($postCatalogue, $request);
            if ($flag == TRUE) {
                $this->updateRouter(
                    $postCatalogue,
                    $request,
                    $this->controllerName,
                    1
                );
                $this->nestedset = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
                ]);
                $this->nestedset();
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $postCatalogue = $this->postCatalogueRepository->delete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\PostCatalogueController'],
            ]);

            $this->nestedset = new Nestedsetbie([
                'table' => 'post_catalogues',
                'foreignkey' => 'post_catalogue_id',
            ]);
            $this->nestedset();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function breadcrumb($model, $language)
    {
        return $this->postCatalogueRepository->findByCondition([
            ['lft', '<=', $model->lft],
            ['rgt', '>=', $model->rgt],
            config('apps.general.defaultPublish')
        ], ['lft', 'asc']);
    }

    private function createCatalogue($request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        $postCatalogue = $this->postCatalogueRepository->create($payload);
        return $postCatalogue;
    }

    private function updateCatalogue($postCatalogue, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->postCatalogueRepository->update($postCatalogue->id, $payload);
        return $flag;
    }

    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);

            $user = $this->postCatalogueRepository->update($post['modelId'], $payload);
            $this->changeUserStatus($post, $payload[$post['field']]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }


    private function changeUserStatus($post, $value)
    {
        DB::beginTransaction();
        try {
            $array = [];
            if (isset($post['modelId'])) {
                $array[] = $post['modelId'];
            } else {
                $array = $post['id'];
            }
            $payload[$post['field']] = $value;
            $this->postRepository->updateByWhereIn('post_catalogue_id', $array, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }


    private function paginateSelect()
    {
        return [
            'post_catalogues.id',
            'post_catalogues.publish',
            'post_catalogues.image',
            'post_catalogues.level',
            'post_catalogues.order',
            'post_catalogues.name',
            'post_catalogues.canonical',
            'post_catalogues.meta_title',
            'post_catalogues.meta_keyword',
            'post_catalogues.meta_description',
            'post_catalogues.description',
            'post_catalogues.content',
        ];
    }

    private function payload()
    {
        return [
            'parent_id',
            'follow',
            'publish',
            'image',
            'album',
            'name',
            'canonical',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'description',
            'content',
        ];
    }
}
