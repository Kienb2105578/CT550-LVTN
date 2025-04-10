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
        $response = $this->reviewService->create($request);
        return response()->json($response);
    }

    public function reply(Request $request)
    {
        // Lấy dữ liệu từ request
        $reviewId = $request->input('review_id');
        $replyText = $request->input('reply_text');
        Log::info($replyText);
        Log::info($reviewId);

        if ($reviewId && $replyText) {
            $this->reviewService->reply($reviewId, $replyText);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
