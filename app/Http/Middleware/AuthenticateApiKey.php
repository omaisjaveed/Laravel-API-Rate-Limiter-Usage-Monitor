<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainText = $this->extractBearerToken($request);

        if ($plainText === null || $plainText === '') {
            return $this->unauthorized('API key is missing. Provide Authorization: Bearer {api_key}.');
        }

        $apiKey = ApiKey::query()
            ->with('client')
            ->where('key_hash', hash('sha256', $plainText))
            ->first();

        if ($apiKey === null) {
            return $this->unauthorized('Invalid API key.');
        }

        if ($apiKey->isRevoked()) {
            return $this->unauthorized('API key has been revoked.', 401);
        }

        if ($apiKey->client === null || ! $apiKey->client->isActive()) {
            return $this->unauthorized('API client is inactive.', 403);
        }

        $apiKey->forceFill(['last_used_at' => now()])->save();

        $request->attributes->set('api_key', $apiKey);
        $request->attributes->set('api_client', $apiKey->client);

        return $next($request);
    }

    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (preg_match('/^Bearer\s+(\S+)$/i', $header, $matches) === 1) {
            return $matches[1];
        }

        // Fallback for simple local testing tools.
        $queryKey = $request->query('api_key');

        return is_string($queryKey) ? $queryKey : null;
    }

    private function unauthorized(string $message, int $status = 401): Response
    {
        return response()->json([
            'message' => $message,
            'error' => 'unauthenticated',
        ], $status);
    }
}
