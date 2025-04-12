<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\Interfaces\UserServiceInterface  as UserService;
use App\Repositories\Interfaces\PermissionRepositoryInterface  as PermissionRepository;
use Illuminate\Support\Facades\Auth;

class SidebarComposer
{

    protected $language;
    protected $permissionRepository;
    protected $userService;

    public function __construct(
        $language,
        UserService $userService,
        PermissionRepository $permissionRepository,
    ) {
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
        $languages = collect(config('languages'));

        $view->with('languages', $languages)
            ->with('usercatalogue_id_login', $usercatalogue_id_login)
            ->with('accessibleMenus', $accessibleMenus);
    }
    public function filterAccessibleMenus($permissions, $menus)
    {
        $allowedRoutes = collect($permissions)->pluck('canonical')->toArray();
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
}
