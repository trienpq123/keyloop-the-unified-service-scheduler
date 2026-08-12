<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Assigns a unique request identifier to every inbound HTTP request.
 *
 * - Accepts an existing X-Request-ID header from the client (useful for
 *   end-to-end tracing through a gateway or load balancer).
 * - Generates a new UUID v7 when none is provided.
 * - Injects the ID into the shared Log context so every log entry written
 *   during the request lifecycle carries it automatically.
 * - Reflects the final ID back to the caller as an X-Request-ID response
 *   header.
 */
final class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID') ?: (string) Str::uuid();

        $request->headers->set('X-Request-ID', $requestId);

        Log::withContext(['request_id' => $requestId]);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
