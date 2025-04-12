<?php

namespace App\Services\Interfaces;

/**
 * Interface UserCatalogueServiceInterface
 * @package App\Services\Interfaces
 */
interface PostServiceInterface
{
    public function paginate($request = null, $postCatalogue = null, $page = 1, $extend = []);
    public function create($request);
    public function update($id, $request);
    public function destroy($id);
}
