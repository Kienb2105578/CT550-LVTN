<?php

namespace App\Services;

use App\Services\Interfaces\AttributeCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class AttributeCatalogueService extends BaseService implements AttributeCatalogueServiceInterface
{


    protected $attributeCatalogueRepository;
    protected $routerRepository;
    protected $nestedset;
    protected $language;
    protected $controllerName = 'AttributeCatalogueController';


    public function __construct(
        AttributeCatalogueRepository $attributeCatalogueRepository,
        RouterRepository $routerRepository,
    ) {
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->routerRepository = $routerRepository;
    }

    public function paginate($request)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),

        ];
        $attributeCatalogues = $this->attributeCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'attribute.catalogue.index'],
        );

        return $attributeCatalogues;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $attributeCatalogue = $this->createCatalogue($request);
            if ($attributeCatalogue->id > 0) {
                $this->createRouter($attributeCatalogue, $request, $this->controllerName);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $attributeCatalogue = $this->attributeCatalogueRepository->findById($id);
            $flag = $this->updateCatalogue($attributeCatalogue, $request);
            if ($flag == TRUE) {
                $this->updateRouter(
                    $attributeCatalogue,
                    $request,
                    $this->controllerName,
                );
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
            $this->attributeCatalogueRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            die();
            return false;
        }
    }

    private function createCatalogue($request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        $attributeCatalogue = $this->attributeCatalogueRepository->create($payload);
        return $attributeCatalogue;
    }

    private function updateCatalogue($attributeCatalogue, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->attributeCatalogueRepository->update($attributeCatalogue->id, $payload);
        return $flag;
    }

    private function paginateSelect()
    {
        return [
            'attribute_catalogues.id',
            'attribute_catalogues.publish',
            'attribute_catalogues.image',
            'attribute_catalogues.name',
            'attribute_catalogues.description',
            'attribute_catalogues.canonical'
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
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical'
        ];
    }
}
