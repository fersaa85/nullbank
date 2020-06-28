<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequestsMiddleware extends \Illuminate\Routing\Middleware\ThrottleRequests
{

    /**
     * Create a 'too many attempts' JSON response.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildJsonResponse($key, $maxAttempts)
    {
        $response = new JsonResponse([
            'error' => [
                'code' => 429,
                'message' => 'Too Many Attempts 2.',
            ],
        ], 429);

        $retryAfter = $this->limiter->availableIn($key);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );
    }
}