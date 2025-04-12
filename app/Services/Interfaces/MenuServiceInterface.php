<?php

namespace App\Services\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface MenuServiceInterface
{
    public function save($request);
    public function convertMenu($menuList = null);
    public function getAndConvertMenu($menu = null);
    public function destroyMenu($id);
    public function dragUpdate(array $json = [], int $menuCatalogueId = 0, $parentId = 0);
    public function saveChildren($request, $menu);
    public function destroy($id);
}
