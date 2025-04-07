<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Interfaces\LanguageRepositoryInterface  as LanguageRepository;
use App\Services\Interfaces\UserServiceInterface  as UserService;
use App\Repositories\Interfaces\PermissionRepositoryInterface  as PermissionRepository;
use Illuminate\Support\Facades\Auth;

class LanguageComposer
{

    protected $language;
    protected $permissionRepository;
    protected $languageRepository;
    protected $userService;

    public function __construct(
        LanguageRepository $languageRepository,
        $language,
        UserService $userService,
        PermissionRepository $permissionRepository,
    ) {
        $this->languageRepository = $languageRepository;
        $this->userService = $userService;
        $this->permissionRepository = $permissionRepository;
    }

    public function compose(View $view)
    {
        $menus = __('sidebar.module');
        $user = Auth::user();
        $usercatalogue_id_login = $user ? $user->user_catalogue_id : null;
        $user_login = $this->userService->getUserPermissions();
        $accessibleMenus = $this->filterAccessibleMenus($user_login, $menus);
        $languages = $this->languageRepository->findByCondition(...$this->agrument());
        $view->with('languages', $languages)
            ->with('usercatalogue_id_login', $usercatalogue_id_login)
            ->with('accessibleMenus', $accessibleMenus);
    }
    public function filterAccessibleMenus($permissions, $menus)
    {
        // Lấy danh sách canonical từ danh sách quyền
        $allowedRoutes = collect($permissions)->pluck('canonical')->toArray();
        //$allowedRoutes = $this->permissionRepository->getAllPermissions();

        // Lọc menu mà user có quyền truy cập
        foreach ($menus as &$menu) {
            if (isset($menu['subModule'])) {
                $menu['subModule'] = array_filter($menu['subModule'], function ($sub) use ($allowedRoutes) {
                    return in_array(str_replace('/', '.', $sub['route']), $allowedRoutes);
                });
            }
        }
        return array_filter($menus, function ($menu) {
            return !empty($menu['subModule']);
        });
    }

    private function agrument()
    {
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [],
            'orderBy' => ['current', 'desc']
        ];
    }
}
