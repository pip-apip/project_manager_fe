<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;

class CategoryAdmController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        //
    }

    public function index()
    {
        if (!request()->has('search')) {
            session()->forget('q');
        }

        $q = Session::get('q');
        $data['q'] = $q;

        $page = request('page', 1);
        $perPage = request()->has('per_page') ? request('per_page') : 10;

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/admin-doc-categories/search', [
            'name' => $q,
            'limit' => $perPage,
            'page' => $page
        ]);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch categories.');
        }

        $total = $response->json()['pagination']['total'] ?? null;
        $category = is_array($response->json()['data'] ?? null) ? $response->json()['data'] : null;
        $results = "";

        if (!is_array($category) || empty($category)) {
            $results = null;
        } else {
            $results = new LengthAwarePaginator(
                collect($category),
                $total,
                $perPage,
                $page,
                ['path' => url('categoryAdm')]
            );
        }

        return view('pages.categoryAdm.index', compact('results'))->with(['title' => 'categoryAdm']);
    }

    public function filter(Request $request)
    {
        $q = $request->input('q', '');
        session(['q' => $q]);

        return redirect()->route('categoryAdm.index', ['search' => $q]);
    }

    public function reset()
    {
        session()->forget('q');
        return redirect()->route('categoryAdm.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = [];
        return view('pages.categoryAdm.form', compact('category'))->with(['title' => 'categoryAdm', 'status' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->post('https://bepm.hanatekindo.com/api/v1/admin-doc-categories?limit=1000', [
            'name' => $request->input('name'),
        ]);

        if ($response->json()['status'] == 400) {
            $errors = $response->json()['errors'];

            // Return the errors to the view, keeping old input data
            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('categoryAdm.index')->with('success', 'Category Administration created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->get("https://bepm.hanatekindo.com/api/v1/admin-doc-categories/{$id}");

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch category details.');
        }

        $category = $response->json()['data'][0];

        return view('pages.categoryAdm.form', compact('category'))->with(['title' => 'categoryAdm', 'status' => 'edit']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->patch('https://bepm.hanatekindo.com/api/v1/admin-doc-categories/'.$id, [
            'name' => $request->input('name'),
        ]);

        if ($response->json()['status'] == 400) {
            $errors = $response->json()['errors'];

            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('categoryAdm.index')->with('success', 'Category Administration update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->delete('https://bepm.hanatekindo.com/api/v1/admin-doc-categories/'.$id);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to delete category.');
        }

        return redirect()->route('categoryAdm.index')->with('success', 'Category Administration delete successfully.');
    }
}
