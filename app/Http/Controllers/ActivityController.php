<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;

class ActivityController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $checkIsProcess = Http::withToken(session('user.access_token'))->get(env('API_BASE_URL').'/users/'. session('user.id'));
        $this->isProcess = $checkIsProcess->json()['data'][0]['is_process'] ?? null;
    }

    public function index()
    {
        if (!request()->has('search')) {
            session()->forget('q');
        }
        if (!request()->has('start_date')) {
            session()->forget('start_date');
        }
        if (!request()->has('end_date')) {
            session()->forget('end_date');
        }

        $q = session('q', '');
        $start_date = session('start_date', '');
        $end_date = session('end_date', '');

        $page = request('page', 1);
        $perPage = request()->has('per_page') ? request('per_page') : 10;

        $this->lastRoute = Route::currentRouteName();

        $accessToken = session('user.access_token');

        $params = [
            'limit' => $perPage,
            'page' => $page,
        ];

        if (!empty($q)) {
            $params[session('user.role') == 'SUPERADMIN' ? 'title' : 'name'] = $q;
        }

        if (!empty($start_date)) {
            $params['start_date'] = date('Y-m-d', strtotime($start_date));
        }

        if (!empty($end_date)) {
            $params['end_date'] = date('Y-m-d', strtotime($end_date));
        }

        if (session('user.role') != 'SUPERADMIN') {
            $project_ids = session('user.project_id', []);
            $params['project_id'] = is_array($project_ids) ? implode(',', $project_ids) : $project_ids;
        }

        $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activities/search', $params);

        // tag all
        // https://bepm.hanatekindo.com/api/v1/activities/search?tags='possimus', 'asdasd'&description='possimus', 'asdasd'

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activities.');
        }

        $total = $response->json()['pagination']['total'] ?? null;
        $activities = is_array($response->json()['data'] ?? null) ? $response->json()['data'] : null;
        $results = "";

        if (!is_array($activities) || empty($activities)) {
            $results = null;
        } else {
            $results = new LengthAwarePaginator(
                collect($activities),
                $total,
                $perPage,
                $page,
                ['path' => url('activity')]
            );
        }

        return view('pages.activity.index', compact('results'))->with(['title' => 'activity']);
    }


    public function filter(Request $request)
    {
        // dd($request->all());
        $q = $request->input('q', '');
        $start_date = $request->input('start_date', '');
        $end_date = $request->input('end_date', '');

        session(['start_date' => $start_date]);
        session(['end_date' => $end_date]);
        session(['q' => $q]);

        return redirect()->route('activity.index', ['search' => $q, 'start_date' => $start_date, 'end_date' => $end_date]);
        // return response()->json([
        //     'status' => 'success',
        //     'redirect_url' => route('activity.index', [
        //         'search' => $q,
        //         'start_date' => $start_date,
        //         'end_date' => $end_date
        //     ])
        // ]);
    }

    public function reset()
    {
        session()->forget('q');
        session()->forget('start_date');
        session()->forget('end_date');
        return redirect()->route('activity.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projectId = $request->query('project_id');

        $accessToken = session('user.access_token');
        $response;

        if(session('user.role') == 'SUPERADMIN'){
            $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects/search', [
                'limit' => 1000,
            ]);
        } else {
            $project_id = "";
            for($i = 0; $i < count(session('user.project_id')); $i++){
                if($i == 0){
                    $project_id = session('user.project_id')[$i];
                } else {
                    $project_id .= ",".session('user.project_id')[$i];
                }
            }
            $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects/search', [
                'id' => $project_id,
            ]);
        }

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project.');
        }

        $activityCategory = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-categories/search?limit=1000');

        if ($activityCategory->failed()) {
            return redirect()->back()->withErrors('Failed to fetch doc category of activity data.');
        }

        $responseUser = Http::withToken($accessToken)->get(env('API_BASE_URL').'/users/search?limit=1000');

        if ($responseUser->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activity data.');
        }

        $users = $responseUser->json()['data'];
        $projects = $response->json()['data'];
        $activity = [];
        $countDocAct = 0;
        $categoryAct = $activityCategory->json()['data'];

        return view('pages.activity.form', compact('activity', 'projects', 'countDocAct', 'categoryAct', 'users', 'projectId'))->with(['title' => 'activity', 'status' => 'create', 'lastUrl' => session('lastUrl')]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($this->isProcess){
            return redirect()->route('activity.index')->with('error', 'Anda memiliki proses aktivitas yang sedang berlangsung. Silakan selesaikan terlebih dahulu.');
        }
        $activity_teams = json_decode($request->input('activityTeam'), true);
        // dd($activity_teams ?? null);
        // $request->validate([
        //     'project_id' => ['required', 'not_in:#'],
        //     'title' => 'required|string|max:255',
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date',
        // ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->post(env('API_BASE_URL').'/activities', [
            'project_id' => $request->input('project_id'),
            'title' => $request->input('title'),
            'activity_category_id' => $request->input('activity_category_id'),
            'start_date' => date('Y-m-d', strtotime($request->input('start_date'))),
            'end_date' => date('Y-m-d', strtotime($request->input('start_date'))),
            'author_id' => session('user.id'),
        ]);

        $responseIsProcess = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/users/'. session('user.id'), [
            'is_process' => TRUE,
        ]);

        if ($response->json()['status'] !== 201) {
            $errors = $response->json()['errors'];
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $latestActivity = $response->json()['data']['id'];

        if (is_array($activity_teams)) {
            foreach ($activity_teams as $team) {
                $responseTeam = Http::withToken($accessToken)->post(env('API_BASE_URL').'/activity-teams', [
                    'activity_id' => $latestActivity,
                    'user_id' => $team['id'],
                ]);

                if ($responseTeam->json()['status'] === 400 || $responseTeam->json()['status'] === 500) {
                    // return redirect()->back()->withErrors('Failed to fetch activity data.');
                    dd($responseTeam->json());
                }
            }
        }

        $responseIsProcess = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/users/'. session('user.id'), [
            'is_process' => TRUE,
        ]);

        return redirect()->route('activity.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $accessToken = session('user.access_token');
        $responseActivity = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activities/'.$id);

        if ($responseActivity->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activity data.');
        }

        $responseDocActivity = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-docs/search?activity_id='.$id.'&limit=1000');

        if ($responseDocActivity->failed()) {
            return redirect()->back()->withErrors('Failed to fetch doc activity data.');
        }

        $responseCategoryDocActivity = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-categories/search?limit=1000');

        if ($responseCategoryDocActivity->failed()) {
            return redirect()->back()->withErrors('Failed to fetch doc category of activity data.');
        }

        $data = [
            'activity'       => $responseActivity->json()['data'],
            'docActivity'    => $responseDocActivity->json()['data'],
            'categoryDoc'   => $responseCategoryDocActivity->json()['data']
        ];

        // dd($data);

        return view('pages.activity.doc', compact('data'))->with(['title' => 'activity']);
    }
    /**
     * Store a newly created resource doc.
     */

    // public function storeDoc(Request $request)
    // {
    //     $accessToken = session('user.access_token');

    //     $http = Http::withToken($accessToken);

    //     // Attach text fields
    //     $http->attach('title', $request->input('title'))
    //         ->attach('description', $request->input('description'))
    //         ->attach('tags', $request->input('tags'))
    //         ->attach('activity_id', $request->input('activity_id'));

    //     // Attach each file
    //     if ($request->hasFile('files')) {
    //         foreach ($request->file('files') as $index => $file) {
    //             $http->attach(
    //                 "files[$index]",                    // e.g., files[0], files[1]
    //                 file_get_contents($file->getRealPath()),
    //                 $file->getClientOriginalName()
    //             );
    //         }
    //     }

    //     $response = $http->post(env('API_BASE_URL').'/activity-docs');

    //     if ($response->json()['status'] === 201) {
    //         $responseIsProcess = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/users/'. session('user.id'), [
    //             'is_process' => FALSE,
    //         ]);
    //     }

    //     $responseData = $response->json();

    //     if (in_array($responseData['status'], [400, 500])) {
    //         return response()->json([
    //             'status' => $responseData['status'],
    //             'message' => $responseData['message'] ?? 'An error occurred',
    //             'errors' => $responseData['errors'] ?? []
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => 201,
    //         'message' => 'Document uploaded successfully.',
    //         'data' => $responseData
    //     ]);
    // }

    // /**
    //  * Update a document.
    //  */

    // public function updateDoc(Request $request, string $id)
    // {
    //     $accessToken = session('user.access_token');
    //     // $debugData = [];

    //     $http = Http::withToken($accessToken);

    //     // Attach text fields
    //     $http->attach('title', $request->input('title'))
    //         ->attach('description', $request->input('description'))
    //         ->attach('tags', $request->input('tags'));

    //     // 1. Debug file baru
    //     if ($request->hasFile('new_files')) {
    //         foreach ($request->file('new_files') as $index => $file) {
    //             // $debugData[] = [
    //             //     'type' => 'new',
    //             //     'original_name' => $file->getClientOriginalName(),
    //             //     'size_kb' => round($file->getSize() / 1024, 2),
    //             //     'mime_type' => $file->getMimeType(),
    //             // ];
    //             $http->attach(
    //                 "files[$index]",
    //                 file_get_contents($file->getRealPath()),
    //                 $file->getClientOriginalName()
    //             );
    //         }
    //     }

    //     // 2. Debug file update dan path lama
    //     $updateFiles = $request->file('update_files');
    //     $replacePaths = $request->input('replace_paths');
    //     $updateIndexes = $request->input('update_indexes');

    //     if (is_array($updateFiles)) {
    //         foreach ($updateFiles as $i => $file) {
    //             $index = $updateIndexes[$i] ?? 'unknown';
    //             $oldPath = $replacePaths[$i] ?? null;

    //             // $debugData[] = [
    //             //     'type' => 'update',
    //             //     'index' => $index,
    //             //     'original_name' => $file->getClientOriginalName(),
    //             //     'replaced_old_path' => $oldPath,
    //             // ];

    //             $http->attach("replace_files[$index]", $oldPath);

    //             $http->attach(
    //                 "files[$index]",
    //                 file_get_contents($file->getRealPath()),
    //                 $file->getClientOriginalName()
    //             );
    //         }
    //     }

    //     // 3. Debug deleted files
    //     if ($request->has('remove_files')) {
    //         foreach ($request->input('remove_files') as $index => $path) {
    //             // $debugData[] = [
    //             //     'type' => 'deleted',
    //             //     'deleted_path' => $path,
    //             // ];
    //             $http->attach("remove_files[$index]", $path);
    //         }
    //     }

    //     $response = $http->post(env('API_BASE_URL').'/activity-docs/'. $id);

    //     $responseData = $response->json();

    //     if (in_array($responseData['status'], [400, 500])) {
    //         return response()->json([
    //             'status' => $responseData['status'],
    //             'message' => $responseData['message'] ?? 'An error occurred',
    //             'errors' => $responseData['errors'] ?? []
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => 201,
    //         'message' => 'Document updated successfully.',
    //         'data' => $responseData
    //     ]);
    // }

    public function storeDoc(Request $request)
    {
        $accessToken = session('user.access_token');
        $http = Http::withToken($accessToken);

        // Handle text fields
        $fields = ['title', 'description', 'tags', 'activity_id'];
        foreach ($fields as $field) {
            $http = $http->attach($field, $request->input($field));
        }

        // Attach uploaded files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $content = @file_get_contents($file->getRealPath());
                if ($content === false) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Gagal membaca file: ' . $file->getClientOriginalName(),
                    ]);
                }
                $http = $http->attach("files[$index]", $content, $file->getClientOriginalName());
            }
        }

        // Submit the request
        $response = $http->post(env('API_BASE_URL').'/activity-docs');
        $responseData = $response->json();

        if (in_array($responseData['status'], [400, 500])) {
            return response()->json([
                'status' => $responseData['status'],
                'message' => $responseData['message'] ?? 'Terjadi kesalahan.',
                'errors' => $responseData['errors'] ?? [],
            ]);
        }

        // Update is_process
        if ($responseData['status'] === 201) {
            Http::withToken($accessToken)->patch(
                env('API_BASE_URL').'/users/' . session('user.id'),
                ['is_process' => false]
            );
        }

        return response()->json([
            'status' => 201,
            'message' => 'Dokumen berhasil diunggah.',
            'data' => $responseData,
        ]);
    }

    public function updateDoc(Request $request, string $id)
    {
        $accessToken = session('user.access_token');
        $http = Http::withToken($accessToken);

        // Attach basic fields
        $fields = ['title', 'description', 'tags'];
        foreach ($fields as $field) {
            $http = $http->attach($field, $request->input($field));
        }

        // Handle new files
        if ($request->hasFile('new_files')) {
            foreach ($request->file('new_files') as $index => $file) {
                $content = @file_get_contents($file->getRealPath());
                if ($content === false) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Gagal membaca file baru: ' . $file->getClientOriginalName(),
                    ]);
                }
                $http = $http->attach("files[$index]", $content, $file->getClientOriginalName());
            }
        }

        // Handle updated files
        $updateFiles = $request->file('update_files', []);
        $replacePaths = $request->input('replace_paths', []);
        $updateIndexes = $request->input('update_indexes', []);

        foreach ($updateFiles as $i => $file) {
            $index = $updateIndexes[$i] ?? $i;
            $oldPath = $replacePaths[$i] ?? null;

            if ($oldPath) {
                $http = $http->attach("replace_files[$index]", $oldPath);
            }

            $content = @file_get_contents($file->getRealPath());
            if ($content === false) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Gagal membaca file update: ' . $file->getClientOriginalName(),
                ]);
            }

            $http = $http->attach("files[$index]", $content, $file->getClientOriginalName());
        }

        // Handle file deletions
        foreach ($request->input('remove_files', []) as $index => $path) {
            $http = $http->attach("remove_files[$index]", $path);
        }

        // Submit to API (using POST as per your flow)
        $response = $http->post("https://bepm.hanatekindo.com/api/v1/activity-docs/{$id}");
        $responseData = $response->json();

        if (in_array($responseData['status'], [400, 500])) {
            return response()->json([
                'status' => $responseData['status'],
                'message' => $responseData['message'] ?? 'Terjadi kesalahan.',
                'errors' => $responseData['errors'] ?? [],
            ]);
        }

        return response()->json([
            'status' => 201,
            'message' => 'Dokumen berhasil diperbarui.',
            'data' => $responseData,
        ]);
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $accessToken = session('user.access_token');

        $responseActivity = Http::withToken($accessToken)->get("https://bepm.hanatekindo.com/api/v1/activities/{$id}");

        if ($responseActivity->failed()) {
            return redirect()->back()->withErrors('Failed to fetch category details.');
        }

        $activity = $responseActivity->json()['data'][0];

        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects');

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $projects = $responseProject->json()['data'];

        $responseDocAct = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-docs/search?activity_id='.$id);

        if ($responseDocAct->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $countDocAct = count($responseDocAct->json()['data']);

        $activityCategory = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-categories/search?limit=1000');


        if ($activityCategory->failed()) {
            return redirect()->back()->withErrors('Failed to fetch doc category of activity data.');
        }

        $categoryAct = $activityCategory->json()['data'];

        return view('pages.activity.form', compact('activity', 'projects', 'countDocAct', 'categoryAct'))->with(['title' => 'activity', 'status' => 'edit']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'project_id' => 'not_in:#',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $accessToken = session('user.access_token');

        // dd($request->all());
        $response = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/activities/'.$id, [
            'project_id' => $request->input('project_id'),
            'title' => $request->input('title'),
            'start_date' => date('Y-m-d', strtotime($request->input('start_date'))),
            'end_date' => date('Y-m-d', strtotime($request->input('end_date'))),
        ]);

        // dd($response->json());

        if ($response->json()['status'] == 400 || $response->json()['status'] == 500) {
            $errors = $response->json()['errors'];

            // Return the errors to the view, keeping old input data
            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('activity.index')->with('success', 'Project edited successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $accessToken = session('user.access_token');

        $responseGet = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activities/'.$id);

        $author_id = $responseGet->json()['data'][0]['author_id'] ?? null;
        $acitivity_doc_status = $responseGet->json()['data'][0]['activity_doc'] ?? null;

        if ($acitivity_doc_status) {
            return redirect()->back()->with('error', 'Aktivitas tidak dapat dihapus karena sudah memiliki dokumen.');
        }

        $responseDelete = Http::withToken($accessToken)->delete(env('API_BASE_URL').'/activities/'.$id);

        if ($responseDelete->json()['status'] !== 200) {
            $errors = $responseDelete->json()['errors'];

            return redirect()->back()->withInput()->withErrors($errors);
        }

        $responseIsProcess = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/users/'. $author_id, [
            'is_process' => false,
        ]);

        return redirect()->route('activity.index')->with('success', 'Activity deleted successfully.');
    }

    public function destroyDoc(string $id)
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->delete(env('API_BASE_URL').'/activity-docs/'.$id);

        if ($response->json()['status'] == 400 || $response->json()['status'] == 500) {
            $errors = $response->json()['errors'];

            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Doc Activity update successfully.');
    }
}
