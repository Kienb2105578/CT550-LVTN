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

    private function initialize($languageId)
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'menus',
            'foreignkey' => 'menu_id',
            'isMenu' => TRUE,
            'language_id' =>  $languageId,
        ]);
    }

    public function paginate($request, $languageId)
    {
        return [];
    }

    public function save($request, $languageId)
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

                    if ($menuSave->id > 0) {
                        // Cập nhật dữ liệu vào bảng liên kết (nếu có)
                        $payloadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key],
                        ];
                        // Lưu vào bảng pivot, nếu cần
                        //$this->menuRepository->createPivot($menuSave, $payloadLanguage, 'languages');
                    }
                }

                $this->initialize($languageId);
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


    public function saveChildren($request, $languageId, $menu)
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

                    if ($menuSave->id > 0) {
                        // Cập nhật dữ liệu vào bảng liên kết (nếu có)
                        $payloadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key],
                        ];
                        // Lưu vào bảng pivot, nếu cần
                        //$this->menuRepository->createPivot($menuSave, $payloadLanguage, 'languages');
                    }
                }


                $this->initialize($languageId);
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

    public function dragUpdate(array $json = [], int $menuCatalogueId = 0, int $languageId = 1, $parentId = 0)
    {
        if (count($json)) {
            foreach ($json as $key => $val) {
                $update = [
                    'order' => count($json) - $key,
                    'parent_id' => $parentId,
                ];

                $menu = $this->menuRepository->update($val['id'], $update);
                if (isset($val['children']) && count($val['children'])) {
                    $this->dragUpdate($val['children'], $menuCatalogueId, $languageId, $val['id']);
                }
            }
        }
        $this->initialize($languageId);
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
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroyMenu($id, $languageId)
    {
        DB::beginTransaction();
        try {
            $menu = $this->menuRepository->findById($id);
            $this->menuRepository->forceDeleteByCondition([
                ['lft', '>=', $menu->lft],
                ['lft', '<=', $menu->rgt],
            ]);
            $this->initialize($languageId);
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

    public function getAndConvertMenu($menu = null, $language = 1): array
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

    public function findMenuItemTranslate($menus, int $currentLanguage = 1, int $languageId = 1)
    {
        $output = [];
        if (count($menus)) {
            foreach ($menus as $key => $menu) {
                $canonical = $menu->canonical; // Lấy canonical từ menu mà không cần đến languages
                $detailMenu = $this->menuRepository->findById($menu->id, ['*'], []);
                if ($detailMenu) {
                    // Cập nhật name và canonical trực tiếp từ bảng menus
                    $menu->translate_name = $detailMenu->name;
                    $menu->translate_canonical = $detailMenu->canonical;
                }
                $output[] = $menu;
            }
        }
        return $output;
    }


    public function saveTranslateMenu($request, int $languageId = 1)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('translate');
            if (count($payload['translate']['name'])) {
                foreach ($payload['translate']['name'] as $key => $val) {
                    if ($val == null) continue;
                    $menu = $this->menuRepository->findById($payload['translate']['id'][$key]);

                    // Cập nhật name và canonical trực tiếp vào bảng menus
                    $menuArray = [
                        'name' => $val,
                        'canonical' => $payload['translate']['canonical'][$key],
                    ];

                    $this->menuRepository->update($menu->id, $menuArray);
                }
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
}
