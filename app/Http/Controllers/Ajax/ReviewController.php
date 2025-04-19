<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ReviewServiceInterface  as ReviewService;
use Illuminate\Support\Facades\Log;


class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(
        ReviewService $reviewService,
    ) {
        $this->reviewService = $reviewService;
    }

    public function create(Request $request)
    {
        try {
            $this->reviewService->create($request);
            return response()->json([
                'code' => 10,
                'messages' => 'Đánh giá sản phẩm thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 0,
                'messages' => 'Đánh giá sản phẩm thất bại',
            ], 500);
        }
    }


    public function reply(Request $request)
    {

        $reviewId = $request->input('review_id');
        $replyText = $request->input('reply_text');
        if ($reviewId && $replyText) {
            $this->reviewService->reply($reviewId, $replyText);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
