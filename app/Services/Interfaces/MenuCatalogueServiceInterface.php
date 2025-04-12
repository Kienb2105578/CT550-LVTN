<?php

namespace App\Services\Interfaces;

/**
 * Interface UserCatalogueServiceInterface
 * @package App\Services\Interfaces
 */
interface MenuCatalogueServiceInterface
{
    public function paginate($request);
    public function create($request);
    // public function update($id, $request);
    // public function destroy($id);
}
