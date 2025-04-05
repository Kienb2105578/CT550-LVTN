<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class LicenseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $licenseKey = env('LICENSE_KEY');
        $expiryDate = env('LICENSE_EXPIRY_DATE');

        $licenseKey = 'sdadasdasdasd';
        $expiryDate = '2025-12-12';

        if (!$licenseKey || Carbon::parse($expiryDate)->isBefore(Carbon::now())) {
            return redirect()->route('license');
        }

        return $next($request);
    }
}
