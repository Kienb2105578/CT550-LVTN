<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\SlideServiceInterface  as SlideService;


class SlideController extends Controller
{
    protected $slideService;

    public function __construct(
        SlideService $slideService
    ) {
        $this->slideService = $slideService;
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }

    public function order(Request $request)
    {
        $payload = $request->input('payload');
        $flag = $this->slideService->updateSlideOrder($payload);
    }
}
