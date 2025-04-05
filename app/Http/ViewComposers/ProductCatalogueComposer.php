<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\ProductCatalogue;
// use App\Services\Interfaces\WidgetServiceInterface  as WidgetService;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Classes\Nestedsetbie;

class ProductCatalogueComposer
{

    protected $widgetService;
    protected $productCatalogueRepository;

    public function __construct(
        // WidgetService $widgetService,
        ProductCatalogueRepository $productCatalogueRepository,
    ) {
        //    $this->widgetService = $widgetService;
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->initialize();
    }
    private function initialize()
    {
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' =>  1,
        ]);
    }
    public function compose(View $view)
    {

        // $categories = $this->productCatalogueRepository->all();
        $categories  = $this->nestedset->getCategoryTree();
        $view->with('categories', $categories);
    }
}
