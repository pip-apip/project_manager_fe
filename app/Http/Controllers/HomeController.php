<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{

    public function index()
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/dashboard');

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activity doc list.');
        }

        $data = $response->json()['data'];

        return view('pages.home', compact('data'))->with(['title' => 'Home']);
    }
}
