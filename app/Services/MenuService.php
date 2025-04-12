<?php

namespace App\Services;

use App\Services\Interfaces\MenuServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;

/**
 * Class MenuService
 * @package App\Services
 */
class MenuService extends BaseService implements MenuServiceInterface
{
    protected $menuRepository;
    protected $menuCatalogueRepository;
    protected $nestedset;
    protected $routerRepository;

    public function __construct(
        MenuRepository $menuRepository,
        MenuCatalogueRepository $menuCatalogueRepository,
        RouterRepository $routerRepository,
    ) {
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->routerRepository = $routerRepository;
    }

    private function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'menus',
            'foreignkey' => 'menu_id',
            'isMenu' => TRUE,
        ]);
    }


    public function save($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('menu', 'menu_catalogue_id');

            if (count($payload['menu']['name'])) {
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'menu_catalogue_id' => $payload['menu_catalogue_id'],
                        'order' => (int)$payload['menu']['order'][$key],
                        'user_id' => Auth::id(),
                        'name' => $val, // Thêm name vào mảng menu
                        'canonical' => $payload['menu']['canonical'][$key], // Thêm canonical vào mảng menu
                    ];

                    if ($menuId == 0) {
                        $menuSave = $this->menuRepository->create($menuArray);
                    } else {
                        $menuSave = $this->menuRepository->update($menuId, $menuArray);
                        if ($menuSave->rgt - $menuSave->lft > 1) {
                            $this->menuRepository->updateByWhere(
                                [
                                    ['lft', '>', $menuSave->lft],
                                    ['rgt', '<', $menuSave->rgt],
                                ],
                                ['menu_catalogue_id' => $payload['menu_catalogue_id']]
                            );
                        }
                    }
                }

                $this->initialize();
                $this->nestedset();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }


    public function saveChildren($request, $menu)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('menu');

            if (count($payload['menu']['name'])) {
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    Log::info("Val", ['menu', $val]);
                    $menuArray = [
                        'menu_catalogue_id' => $menu->menu_catalogue_id,
                        'parent_id' => $menu->id,
                        'order' => (int)$payload['menu']['order'][$key],
                        'user_id' => Auth::id(),
                        'name' => $val,
                        'canonical' => $payload['menu']['canonical'][$key],
                    ];
                    Log::info("menu", ['menu', $menuArray]);

                    if ($menuId == 0) {
                        $menuSave = $this->menuRepository->create($menuArray);
                    } else {
                        $menuSave = $this->menuRepository->update($menuId, $menuArray);
                    }
                }


                $this->initialize();
                $this->nestedset();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function dragUpdate(array $json = [], int $menuCatalogueId = 0, $parentId = 0)
    {
        if (count($json)) {
            foreach ($json as $key => $val) {
                $update = [
                    'order' => count($json) - $key,
                    'parent_id' => $parentId,
                ];

                $menu = $this->menuRepository->update($val['id'], $update);
                if (isset($val['children']) && count($val['children'])) {
                    $this->dragUpdate($val['children'], $menuCatalogueId, $val['id']);
                }
            }
        }
        $this->initialize();
        $this->nestedset();
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->menuRepository->forceDeleteByCondition([
                ['menu_catalogue_id', '=', $id],
            ]);
            $this->menuCatalogueRepository->forceDelete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroyMenu($id)
    {
        DB::beginTransaction();
        try {
            $menu = $this->menuRepository->findById($id);
            $this->menuRepository->forceDeleteByCondition([
                ['lft', '>=', $menu->lft],
                ['lft', '<=', $menu->rgt],
            ]);
            $this->initialize();
            $this->nestedset();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getAndConvertMenu($menu = null): array
    {
        $menuList = $this->menuRepository->findByCondition([
            ['parent_id', '=', $menu->id]
        ], TRUE, []);
        return $this->convertMenu($menuList);
    }

    public function convertMenu($menuList = null)
    {
        $temp = [];
        $fields = ['name', 'canonical', 'order', 'id'];
        if (count($menuList)) {
            foreach ($menuList as $key => $val) {
                foreach ($fields as $field) {
                    if ($field == 'name' || $field == 'canonical') {
                        $temp[$field][] = $val->{$field};
                    } else {
                        $temp[$field][] = $val->{$field};
                    }
                }
            }
        }
        return $temp;
    }
}
