<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $providedRequestId = (string) $request->header('X-Request-ID', '');
        $requestId = Str::isUuid($providedRequestId) ? $providedRequestId : (string) Str::uuid7();

        $request->headers->set('X-Request-ID', $requestId);

        Log::withContext(['request_id' => $requestId]);
        Log::shareContext(['request_id' => $requestId]);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
