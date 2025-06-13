<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class RefershTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $accessToken = session('user.access_token');

        try {
            // Test API request to check if the token is valid
            $response = Http::withToken($accessToken)
                            ->timeout(10)  // You can adjust the timeout value as needed
                            ->get(env('API_BASE_URL').'/users');

            $responseStatus = $response->json()['status'];

            // If the token is expired (401 Unauthorized), refresh it
            if ($responseStatus === 401) {
                $newAccessToken = $this->refreshAccessToken();

                if ($newAccessToken) {
                    session()->put('user.access_token', $newAccessToken);

                    // Retry the original request with new token
                    return redirect()->refresh();
                }

                // If refresh fails, redirect to login
                return redirect()->route('login')->withErrors('Session expired. Please login again.');
            }

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Handle timeouts and other request errors
            if ($e->getCode() === 28) { // 28 is the error code for timeout in cURL
                Log::error('API request timed out: ' . $e->getMessage());
                return redirect()->route('error.page')->withErrors('The request timed out. Please try again later.');
            }

            // Log any other errors
            Log::error('API request failed: ' . $e->getMessage());
            return redirect()->route('error.page')->withErrors('An error occurred while communicating with the server. Please try again later.');
        }

        return $next($request);
    }

    private function refreshAccessToken()
    {
        $refreshToken = session('user.refresh_token');

        if (!$refreshToken) {
            return null;
        }

        // Send a POST request to refresh the token
        $response = Http::withToken($refreshToken)->post(env('API_BASE_URL').'/auth/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        if ($response->successful()) {
            return $response->json('data.access_token') ?? null;
        }

        return null;
    }
}
