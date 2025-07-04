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
    private $project_leader_id;

    public function __construct()
    {
        $this->project_leader_id = [];
        if (session('user.project_leader')) {
            $this->project_leader_id = session('user.project_leader_id', []);
        }
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

        $params = [
            'limit' => $perPage,
            'page' => $page,
        ];
        $paramsProject = '';

        if (session('user.project_leader')) {
            $project_ids = $this->project_leader_id;
            $params['project_id'] = is_array($project_ids) ? implode(',', $project_ids) : $project_ids;
            $paramsProject = '/search?id='.$params['project_id'] ?? '';
        }

        if (session('project_id')) {
            $params['project_id'] = session('project_id');
        }

        if (session('user.role') == 'SUPERADMIN' || session('user.role') == 'ADMIN') {
            unset($params['project_id']);
        }

        if (session('q')) {
            $params['name'] = session('q');
        }

        $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-categories/search', $params);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch categories.');
        }
        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects'. $paramsProject . '?limit=100');

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

        $paramsProject = '?';
        if (session('user.project_leader')) {
            $project_ids = is_array($this->project_leader_id) ? implode(',', $this->project_leader_id) : $this->project_leader_id;
            $paramsProject = '/search?id='.$project_ids.'&';
        }
        if (session('user.role') == 'SUPERADMIN' || session('user.role') == 'ADMIN') {
            $paramsProject = '?';
        } elseif (!session('user.project_leader')) {
            return redirect()->back()->with('error', 'You do not have permission to create a category activity.');
        }

        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects'. $paramsProject . 'limit=100');

        if ($responseProject->json()['status'] !== 200) {
            return redirect()->back()->with('error','Failed to fetch categories.');
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

        $response = Http::withToken($accessToken)->post(env('API_BASE_URL').'/activity-categories', [
            'name' => $request->input('name'),
            'project_id' => $request->input('project_id'),
        ]);

        if ($response->json()['status'] !== 200 && $response->json()['status'] !== 201) {
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

        $response = Http::withToken($accessToken)->get(env('API_BASE_URL')."/activity-categories/{$id}");

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch category details.');
        }

        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects');

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

        $response = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/activity-categories/'.$id, [
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

        $response = Http::withToken($accessToken)->delete(env('API_BASE_URL').'/activity-categories/'.$id);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to delete category.');
        }

        return redirect()->route('categoryAct.index')->with('success', 'Category Activity delete successfully.');
    }
}
