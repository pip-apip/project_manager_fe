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
        if (!request()->has('search')) {
            session()->forget('q');
        }
        $page = request('page', 1);
        $perPage = request()->has('per_page') ? request('per_page') : 10;

        $params = [
            'limit' => $perPage,
            'page' => $page,
        ];

        if (session('q')) {
            $params['name'] = session('q');
        }

        if (session('user.role') != 'SUPERADMIN' && session('user.role') != 'ADMIN') {
            $project_ids = session('user.project_id', []);
            $params['id'] = is_array($project_ids) ? implode(',', $project_ids) : $project_ids;
        }

        $responseProject = Http::withToken($this->accessToken)->get(env('API_BASE_URL').'/projects/search', $params);

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
                ['path' => url('progress')]
            );
        }

        return view('pages.progress.index', compact('results'))->with('title', 'progress');
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
        $data = [];
        $failedCount = 0;

        foreach ($request->except('_token') as $key => $value) {
            $key_split = explode('_', $key);
            $field = $key_split[0]; // e.g., 'progress' or 'note'
            $id = $key_split[1]; // e.g., '9', '11', '10'

            if (!isset($data[$id])) {
                $data[$id] = ['id' => $id];
            }

            $data[$id][$field] = $value;
        }

        $finalResponse = [];

        foreach ($data as $item) {
            $response = Http::withToken($this->accessToken)->post(env('API_BASE_URL').'/activity-categories/' . $item['id'], [
                'value' => $item['progress'] ?? null,
                'note' => $item['note'] ?? null,
            ]);

            if ($response->json()['status'] !== 200) {
                $failedCount++;
            }
            $finalResponse[] = [
                'id' => $item['id'],
                'response' => $response->json(),
            ];
        }

        if ($failedCount > 0) {
            return redirect()->back()->withErrors('Failed to update progress data.');
        } else {
            return redirect()->back()->with('success', 'Progress data updated successfully.');
        }
    }

    public function storeImage(Request $request, $id)
    {
        $http = Http::withToken($this->accessToken);

        if ($request->hasFile('files')) {
            $uploaded = [];

            // foreach ($request->file('files') as $file) {
            //     $filename = time().'_'.$file->getClientOriginalName();
            //     $path = $file->storeAs('uploads', $filename, 'public');

            //     $uploaded[] = [
            //         'name' => $filename,
            //         'path' => $path,
            //     ];
            // }

            // return response()->json([
            //     'success' => true,
            //     'files' => $uploaded,
            // ]);

            foreach ($request->file('files') as $index => $file) {
                $http->attach(
                    "images[$index]",
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }

            $response = $http->post(env('API_BASE_URL').'/activity-categories/'. $id);

            $responseData = $response->json();

            if (in_array($responseData['status'], [400, 500])) {
                return response()->json([
                    'status' => $responseData['status'],
                    'message' => $responseData['message'] ?? 'An error occurred',
                    'errors' => $responseData['errors'] ?? []
                ]);
            }

            return response()->json([
                'status' => 201,
                'message' => 'Document uploaded successfully.',
                'data' => $responseData
            ]);

        }

        return response()->json([
            'success' => false,
            'message' => 'No files uploaded',
        ], 422);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $responseProject = Http::withToken($this->accessToken)->get(env('API_BASE_URL').'/projects/'.$id);

        if (session('user.role') !== 'SUPERADMIN' && session('user.role') !== 'ADMIN') {
            if ($responseProject->json()['data'][0]['project_leader_id'] !== session('user.id')) {
                return redirect()->back()->with('error', 'You do not have permission to view this project.');
            }
        }

        if ($responseProject->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $responseActivityCategory = Http::withToken($this->accessToken)->get(env('API_BASE_URL').'/activity-categories/search?project_id='.$id);

        if ($responseActivityCategory->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch activity category data.');
        }

        $project = $responseProject->json()['data'][0];
        $activityCategory = $responseActivityCategory->json()['data'];

        return view('pages.progress.project', compact('project','activityCategory'))->with('title', 'progress');
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
    public function updateImage(Request $request, string $id)
    {
        $http = Http::withToken($this->accessToken);
        $debugData = [];

        // 1. Debug file baru
        if ($request->hasFile('new_files')) {
            foreach ($request->file('new_files') as $index => $file) {
                // $debugData[] = [
                //     'type' => 'new',
                //     'original_name' => $file->getClientOriginalName(),
                //     'size_kb' => round($file->getSize() / 1024, 2),
                //     'mime_type' => $file->getMimeType(),
                // ];
                $http->attach(
                    "images[$index]",
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }
        }

        // 2. Debug file update dan path lama
        $updateFiles = $request->file('update_files');
        $replacePaths = $request->input('replace_paths');
        $updateIndexes = $request->input('update_indexes');

        if (is_array($updateFiles)) {
            foreach ($updateFiles as $i => $file) {
                $index = $updateIndexes[$i] ?? 'unknown';
                $oldPath = $replacePaths[$i] ?? null;

                // $debugData[] = [
                //     'type' => 'update',
                //     'index' => $index,
                //     'original_name' => $file->getClientOriginalName(),
                //     'replaced_old_path' => $oldPath,
                // ];
                $http->attach("replace_images[$index]", $oldPath);

                $http->attach(
                    "images[$index]",
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }
        }

        // 3. Debug deleted files
        if ($request->has('remove_images')) {
            foreach ($request->input('remove_images') as $index => $path) {
                // $debugData[] = [
                //     'type' => 'deleted',
                //     'deleted_path' => $path,
                // ];
                $http->attach("remove_images[$index]", $path);
            }
        }

        $response = $http->post(env('API_BASE_URL').'/activity-categories/'. $id);

        $responseData = $response->json();

        if (in_array($responseData['status'], [400, 500])) {
            return response()->json([
                'status' => $responseData['status'],
                'message' => $responseData['message'] ?? 'An error occurred',
                'errors' => $responseData['errors'] ?? []
            ]);
        }

        return response()->json([
            'status' => 201,
            'message' => 'Document uploaded successfully.',
            'data' => $responseData
        ]);
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
