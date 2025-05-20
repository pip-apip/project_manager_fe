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

        foreach ($data as $item) {
            $response = Http::withToken($this->accessToken)->post('https://bepm.hanatekindo.com/api/v1/activity-categories/' . $item['id'], [
                'progress' => $item['progress'] ?? null,
                'note' => $item['note'] ?? null,
            ]);

            if ($response->json()['status'] !== 200) {
                $failedCount++;
            }
        }

        if ($failedCount > 0) {
            return redirect()->back()->withErrors('Failed to update progress data.');
        } else {
            return redirect()->route('progress.index')->with('success', 'Progress data updated successfully.');
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

            $response = $http->post('https://bepm.hanatekindo.com/api/v1/activity-categories/'. $id);

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
        $responseProject = Http::withToken($this->accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects/'.$id);

        if ($responseProject->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $responseActivityCategory = Http::withToken($this->accessToken)->get('https://bepm.hanatekindo.com/api/v1/activity-categories/search?project_id='.$id);

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

        $response = $http->post('https://bepm.hanatekindo.com/api/v1/activity-categories/'. $id);

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
