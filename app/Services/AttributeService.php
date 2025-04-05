<?php

namespace App\Services;

use App\Services\Interfaces\AttributeServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class AttributeService
 * @package App\Services
 */
class AttributeService extends BaseService implements AttributeServiceInterface
{
    protected $attributeRepository;
    protected $routerRepository;

    public function __construct(
        AttributeRepository $attributeRepository,
        RouterRepository $routerRepository,
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'AttributeController';
    }

    public function paginate($request, $languageId)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
        ];
        $paginationConfig = [
            'path' => 'attribute.index',
            'groupBy' => $this->paginateSelect()
        ];
        $orderBy = ['attributes.id', 'DESC'];
        $relations = ['attribute_catalogues'];
        $rawQuery = $this->whereRaw($request);
        $attributes = $this->attributeRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            $paginationConfig,
            $orderBy,
            [],
            $relations,
            $rawQuery
        );
        return $attributes;
    }

    private function whereRaw($request)
    {
        $rawCondition = [];
        if ($request->integer('attribute_catalogue_id') > 0) {
            $rawCondition['whereRaw'] = [
                [
                    'attributes.attribute_catalogue_id IN (
                    SELECT id
                    FROM attribute_catalogues
                    WHERE id = ?
                )',
                    [$request->integer('attribute_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $attribute = $this->createAttribute($request);
            if ($attribute->id > 0) {
                //$this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForAttribute($attribute, $request);
                $this->createRouter($attribute, $request, $this->controllerName, $languageId);
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

    public function update($id, $request, $languageId)
    {
        DB::beginTransaction();
        try {
            $attribute = $this->attributeRepository->findById($id);
            if ($this->uploadAttribute($attribute, $request)) {
                //$this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForAttribute($attribute, $request);
                $this->updateRouter(
                    $attribute,
                    $request,
                    $this->controllerName,
                    $languageId
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
            $attribute = $this->attributeRepository->delete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    private function createAttribute($request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $attribute = $this->attributeRepository->create($payload);
        return $attribute;
    }

    private function uploadAttribute($attribute, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->attributeRepository->update($attribute->id, $payload);
    }


    private function updateCatalogueForAttribute($attribute, $request)
    {
        $attribute->attribute_catalogues()->sync($this->catalogue($request));
    }



    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->attribute_catalogue_id]));
        }
        return [$request->attribute_catalogue_id];
    }



    private function paginateSelect()
    {
        return [
            'attributes.id',
            'attributes.publish',
            'attributes.image',
            'attributes.name',
            'attributes.description',
            'attributes.canonical'
        ];
    }

    private function payload()
    {
        return [
            'follow',
            'publish',
            'image',
            'album',
            'attribute_catalogue_id',
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
