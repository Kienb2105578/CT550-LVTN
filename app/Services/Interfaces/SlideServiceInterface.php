<?php

namespace App\Services\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface SlideServiceInterface
{
    public function paginate($request);
    public function destroy($id);
    public function converSlideArray(array $slide = []);
    public function create($request);
    public function update($id, $request);
    public function updateSlideOrder($post);
}
