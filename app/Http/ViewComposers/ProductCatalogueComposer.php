<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Classes\Nestedsetbie;

class ProductCatalogueComposer
{

    protected $productCatalogueRepository;

    protected $nestedset;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->initialize();
    }
    private function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
        ]);
    }
    public function compose(View $view)
    {
        $categories  = $this->nestedset->getCategoryTree();
        $view->with('categories', $categories);
    }
}
