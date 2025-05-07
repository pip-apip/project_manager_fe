<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;

class CategoryActController extends Controller
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

        if (!request()->has('project_id')) {
            session()->forget('project_id');
        }

        $page = request('page', 1);
        $perPage = request()->has('per_page') ? request('per_page') : 10;

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/activity-categories/search', [
            'name' => session('q'),
            'limit' => $perPage,
            'page' => $page,
            'project_id' => session('project_id'),
        ]);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch categories.');
        }

        $responseProject = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects');

        if ($responseProject->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch categories.');
        }

        $projects = $responseProject->json()['data'] ?? null;
        $total = $response->json()['pagination']['total'] ?? null;
        $categories = is_array($response->json()['data'] ?? null) ? $response->json()['data'] : null;
        $results = null;
        if (!is_array($categories) || empty($categories)) {
            $results = null;
        } else {
            $results = new LengthAwarePaginator(
                collect($categories),
                $total,
                $perPage,
                $page,
                ['path' => url('categoryAct')]
            );
        }

        return view('pages.categoryAct.index', compact('results', 'projects'))->with(['title' => 'categoryAct']);
    }

    public function filter(Request $request)
    {
        $q = $request->input('q', '');
        $project_id = $request->input('project_id', '');
        session(['q' => $q]);
        session(['project_id' => $project_id]);

        return redirect()->route('categoryAct.index', ['search' => $q, 'project_id' => $project_id]);
    }

    public function reset()
    {
        session()->forget('q');
        session()->forget('project_id');
        return redirect()->route('categoryAct.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accessToken = session('user.access_token');

        $responseProject = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects');

        if ($responseProject->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch categories.');
        }

        $projects = $responseProject->json()['data'] ?? null;
        $category = [];

        return view('pages.categoryAct.form', compact('category', 'projects'))->with(['title' => 'categoryAct', 'status' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        // ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->post('https://bepm.hanatekindo.com/api/v1/activity-categories', [
            'name' => $request->input('name'),
            'project_id' => $request->input('project_id'),
        ]);

        if ($response->json()['status'] !== 200) {
            $errors = $response->json()['errors'];

            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('categoryAct.index')->with('success', 'Category Activity created successfully.');
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

        $response = Http::withToken($accessToken)->get("https://bepm.hanatekindo.com/api/v1/activity-categories/{$id}");

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch category details.');
        }

        $responseProject = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects');

        if ($responseProject->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch categories.');
        }

        $projects = $responseProject->json()['data'] ?? null;
        $category = $response->json()['data'][0];

        return view('pages.categoryAct.form', compact('category', 'projects'))->with(['title' => 'categoryAct', 'status' => 'edit']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        // ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->patch('https://bepm.hanatekindo.com/api/v1/activity-categories/'.$id, [
            'name' => $request->input('name'),
            'project_id' => $request->input('project_id'),
        ]);

        if ($response->json()['status'] == 400) {
            $errors = $response->json()['errors'];

            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('categoryAct.index')->with('success', 'Category Activity update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->delete('https://bepm.hanatekindo.com/api/v1/activity-categories/'.$id);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to delete category.');
        }

        return redirect()->route('categoryAct.index')->with('success', 'Category Activity delete successfully.');
    }
}
