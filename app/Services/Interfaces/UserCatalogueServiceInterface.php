<?php

namespace App\Services\Interfaces;

/**
 * Interface UserCatalogueServiceInterface
 * @package App\Services\Interfaces
 */
interface UserCatalogueServiceInterface
{
    public function paginate($request);
    public function setPermission($request);

    public function create($request);
    public function update($id, $request);
    public function destroy($id);
}
