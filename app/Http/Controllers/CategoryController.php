<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{
    public function admin()
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/admin-doc-categories');

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch categories.');
        }

        $categories = $response->json();
        // dd($categories);

        return view('pages.categoryAdm.index', compact('categories'))->with(['title' => 'categoryAdm']);
    }

    public function formAdm(){
        return view('pages.categoryAdm.form')->with(['title' => 'categoryAdm']);
    }

    public function activity()
    {
        return view('pages.categoryActivity', ['title' => 'categoryAct']);
    }
}
