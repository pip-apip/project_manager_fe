<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('user')) {
            return redirect('/login')->withErrors(['error' => 'Anda harus login terlebih dahulu']);
        }

        // $loginTime = session('login-time');

        // if ($loginTime && now()->diffInMinutes(Carbon::parse($loginTime)) >= 5) {
        //     $token = session('user.access_token');

        //     try {
        //         Http::withToken($token)->post('https://bepm.hanatekindo.com/api/v1/auth/logout');
        //     } catch (\Exception $e) {
        //         return redirect('/login')->withErrors(['error' => 'Sesi telah berakhir, silakan login kembali']);
        //     }

        //     session()->forget(['user', 'login-time']);
        //     session()->flush();

        //     return redirect('/login')->withErrors(['error' => 'Sesi telah berakhir, silakan login kembali']);
        // }

        $currentRoute = Route::currentRouteName();
        $id = request()->route('id');
        $currentRoute .= $id ? ',' . $id : '';

        $previousRoute = session('currentRoute');

        if ($previousRoute !== $currentRoute) {
            session()->put('lastRoute', $previousRoute);
        }

        session()->put('currentRoute', $currentRoute);
        // dd(session('lastRoute'), session('currentRoute'));

        return $next($request);
    }
}

