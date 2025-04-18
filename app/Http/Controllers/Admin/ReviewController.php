<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\ReviewServiceInterface  as ReviewService;
use App\Repositories\Interfaces\ReviewRepositoryInterface as ReviewRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;

class ReviewController extends Controller
{
    protected $reviewService;
    protected $reviewRepository;
    protected $productRepository;

    public function __construct(
        ReviewService $reviewService,
        ReviewRepository $reviewRepository,
        ProductRepository $productRepository,
    ) {
        $this->reviewService = $reviewService;
        $this->reviewRepository = $reviewRepository;
        $this->productRepository = $productRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'review.index');
        $reviews = $this->reviewService->paginate($request);
        $reviews = $this->productRepository->addProductCanonicalToReviews($reviews);

        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Review'
        ];
        $config['seo'] = __('messages.review');
        $template = 'admin.review.index';
        return view('admin.dashboard.layout', compact(
            'template',
            'config',
            'reviews'
        ));
    }


    public function destroy($id)
    {

        if ($this->reviewRepository->delete($id)) {
            return redirect()->route('review.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('review.index')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }


    private function config()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugins/ckeditor/ckeditor.js',
            ]
        ];
    }
}
