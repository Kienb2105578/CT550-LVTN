<?php

namespace App\Services\Interfaces;

/**
 * Interface ProductServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductServiceInterface
{
    public function paginate($request, $productCatalogue = null, $page = 1, $extend = []);
    public function create($request);
    public function update($id, $request);
    public function destroy($id);
}
