<?php

namespace App\Services\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface AttributeCatalogueServiceInterface
{

    public function paginate($request);
    public function create($request);
    public function update($id, $request);
    public function destroy($id);
}
