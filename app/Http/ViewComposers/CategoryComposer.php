<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface  as ProductCatalogueRepository;

class CategoryComposer
{

    protected $productCatalogueRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
    }

    public function compose(View $view)
    {
        $category = $this->productCatalogueRepository->all();
        $category = recursive($category);
        $view->with('category', $category);
    }
}
