<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;

class ProgressController extends Controller
{
    public function __construct()
    {
        $this->accessToken = session('user.access_token');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = request('page', 1);
        $perPage = request()->has('per_page') ? request('per_page') : 10;

        $params = [
            'limit' => $perPage,
            'page' => $page,
        ];

        $responseProject = Http::withToken($this->accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects/search', $params);

        if ($responseProject->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $total = $responseProject->json()['pagination']['total'] ?? null;
        $results = '';
        if (!is_array($responseProject->json()['data']) || empty($responseProject->json()['data'])) {
            $results = [];
        }else{
            $results = new LengthAwarePaginator(
                collect($responseProject->json()['data']),
                $total,
                $perPage,
                $page,
                ['path' => url('project')]
            );
        }

        return view('pages.progress.index', compact('results'))->with('title', 'Progress');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $responseProject = Http::withToken($this->accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects/'.$id);

        if ($responseProject->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $responseActivityCategory = Http::withToken($this->accessToken)->get('https://bepm.hanatekindo.com/api/v1/activity-categories/search?project_id='.$id.',0');

        if ($responseActivityCategory->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch activity category data.');
        }

        $project = $responseProject->json()['data'][0];
        $activityCategory = $responseActivityCategory->json()['data'];

        return view('pages.progress.project', compact('project','activityCategory'))->with('title', 'Progress');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
