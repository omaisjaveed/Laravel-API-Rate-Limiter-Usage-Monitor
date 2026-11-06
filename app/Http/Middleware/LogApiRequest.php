<?php

namespace App\Http\Middleware;

use App\Services\ApiRequestLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    public function __construct(
        private readonly ApiRequestLogger $logger,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = hrtime(true);

        /** @var Response $response */
        $response = $next($request);

        $this->logger->log($request, $response, $startedAt);

        return $response;
    }
}
