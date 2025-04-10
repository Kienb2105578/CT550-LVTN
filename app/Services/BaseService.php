<?php

namespace App\Services;

use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use App\Models\Language;
use Illuminate\Support\Str;

/**
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{

    protected $routerRepository;
    protected $controllerName;
    protected $language;
    protected $nestedset;

    public function __construct(
        RouterRepository $routerRepository
    ) {
        $this->routerRepository = $routerRepository;
    }

    public function currentLanguage()
    {
        $locale = app()->getLocale();
        return 1;
    }

    public function formatAlbum($request)
    {
        return ($request->input('album') && !empty($request->input('album'))) ? json_encode($request->input('album')) : '';
    }

    public function formatJson($request, $inputName)
    {
        return ($request->input($inputName) && !empty($request->input($inputName))) ? json_encode($request->input($inputName)) : '';
    }

    public function nestedset()
    {
        $this->nestedset->Get('level ASC, order ASC');
        $this->nestedset->Recursive(0, $this->nestedset->Set());
        $this->nestedset->Action();
    }

    public function formatRouterPayload($model, $request, $controllerName, $languageId)
    {
        $router = [
            'canonical' => Str::slug($request->input('canonical')),
            'module_id' => $model->id,
            'language_id' => $languageId,
            'controllers' => 'App\Http\Controllers\Frontend\\' . $controllerName . '',
        ];
        return $router;
    }

    public function createRouter($model, $request, $controllerName, $languageId)
    {
        $router = $this->formatRouterPayload($model, $request, $controllerName, 1);
        $this->routerRepository->create($router);
    }


    public function updateRouter($model, $request, $controllerName, $languageId)
    {
        $payload = $this->formatRouterPayload($model, $request, $controllerName, $languageId);
        $condition = [
            ['module_id', '=', $model->id],
            ['language_id', '=', $languageId],
            ['controllers', '=', 'App\Http\Controllers\Frontend\\' . $controllerName],
        ];
        $router = $this->routerRepository->findByCondition($condition);
        $res = $this->routerRepository->update($router->id, $payload);
        return $res;
    }

    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $model = lcfirst($post['model']) . 'Repository';
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            $post = $this->{$model}->update($post['modelId'], $payload);

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

    public function updateStatusAll($post)
    {
        DB::beginTransaction();
        try {
            $model = lcfirst($post['model']) . 'Repository';
            $payload[$post['field']] = $post['value'];
            $flag = $this->{$model}->updateByWhereIn('id', $post['id'], $payload);
            //$this->changeUserStatus($post, $post['value']);

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
}
