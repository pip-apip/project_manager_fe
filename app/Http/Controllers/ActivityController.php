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
        // dd(session('currentRoute'), session('lastRoute'));
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

        if($this->isProcess && session('user.role') !== 'SUPERADMIN'){
            return redirect()->route('activity.index')->with('error', 'Anda memiliki proses aktivitas yang sedang berlangsung. Silakan selesaikan terlebih dahulu.');
        }
        $activity_teams = json_decode($request->input('activityTeam'), true);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->post(env('API_BASE_URL').'/activities', [
            'project_id' => $request->input('project_id'),
            'title' => $request->input('title'),
            'activity_category_id' => $request->input('activity_category_id'),
            'start_date' => date('Y-m-d', strtotime($request->input('start_date'))),
            'end_date' => date('Y-m-d', strtotime($request->input('start_date'))),
            'author_id' => session('user.id'),
        ]);

        if ($response->json()['status'] !== 201) {
            $errors = $response->json()['errors'];
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $responseIsProcess = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/users/'. session('user.id'), [
            'is_process' => TRUE,
        ]);

        $latestActivity = $response->json()['data']['id'];

        if (is_array($activity_teams)) {
            foreach ($activity_teams as $team) {
                $responseTeam = Http::withToken($accessToken)->post(env('API_BASE_URL').'/activity-teams', [
                    'activity_id' => $latestActivity,
                    'user_id' => $team['id'],
                ]);

                if ($responseTeam->json()['status'] === 400 || $responseTeam->json()['status'] === 500) {
                    return redirect()->back()->with('error', 'Failed to fetch activity data.');
                    // dd($responseTeam->json());
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

        $responseActivityTeam = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-teams/search?activity_id='.$id.'&limit=1000');

        if ($responseActivityTeam->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activity team data.');
        }

        $data = [
            'activity'       => $responseActivity->json()['data'],
            'docActivity'    => $responseDocActivity->json()['data'],
            'categoryDoc'   => $responseCategoryDocActivity->json()['data'],
            'activityTeam'   => $responseActivityTeam->json()['data'],
        ];

        // dd($data);

        return view('pages.activity.doc', compact('data'))->with(['title' => 'activity']);
    }
    /**
     * Store a newly created resource doc.
     */

    public function storeDoc(Request $request)
    {
        $accessToken = session('user.access_token');
        $http = Http::withToken($accessToken);

        // Start building the multipart request
        $multipart = [];

        // Basic fields
        $multipart[] = ['name' => 'title', 'contents' => $request->input('title')];
        $multipart[] = ['name' => 'location', 'contents' => $request->input('location')];
        $multipart[] = ['name' => 'date', 'contents' => date('Y-m-d', strtotime($request->input('date')))];
        $multipart[] = ['name' => 'activity_id', 'contents' => $request->input('activity_id')];

        // Arrays: decode from JSON and add each item individually
        $agenda = json_decode($request->input('agenda'), true) ?? [];
        foreach ($agenda as $index => $item) {
            $multipart[] = ['name' => "agenda[$index]", 'contents' => $item];
        }

        $activity = json_decode($request->input('activity'), true) ?? [];
        foreach ($activity as $index => $item) {
            $multipart[] = ['name' => "activity[$index]", 'contents' => $item];
        }

        $person = json_decode($request->input('person'), true) ?? [];
        foreach ($person as $index => $item) {
            $multipart[] = ['name' => "meet_of_person[$index]", 'contents' => $item];
        }

        $tags = json_decode($request->input('tags'), true) ?? [];
        foreach ($tags as $index => $item) {
            $multipart[] = ['name' => "tags[$index]", 'contents' => $item];
        }

        // Files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $multipart[] = [
                    'name'     => "files[$index]",
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }
        }

        // Send the request
        $response = Http::withToken($accessToken)
            ->asMultipart()
            ->post(env('API_BASE_URL') . '/activity-docs', $multipart);

        $responseData = $response->json();

        if (in_array($responseData['status'], [400, 500])) {
            return response()->json([
                'status' => $responseData['status'],
                'message' => $responseData['message'] ?? 'An error occurred',
                'errors' => $responseData['errors'] ?? []
            ]);
        }

        // Optional: patch user status
        if ($responseData['status'] === 201) {
            Http::withToken($accessToken)->patch(env('API_BASE_URL') . '/users/' . session('user.id'), [
                'is_process' => false,
            ]);
        }

        return response()->json([
            'status' => 201,
            'message' => 'Document uploaded successfully.',
            'data' => $responseData
        ]);
    }


    /**
     * Update a document.
     */

    public function updateDoc(Request $request, string $id)
    {
        // return response()->json($request->all());
        $accessToken = session('user.access_token');

        $multipart = [
            ['name' => 'title', 'contents' => $request->input('title')],
            ['name' => 'location', 'contents' => $request->input('location')],
            ['name' => 'date', 'contents' => date('Y-m-d', strtotime($request->input('date')))],
            // ['name' => 'tags', 'contents' => $request->input('tags')],
        ];

        $agenda = json_decode($request->input('agenda'), true) ?? [];
        if(empty($agenda)){
            $multipart[] = ['name' => 'agenda', 'contents' => ""];
        }else{
            foreach ($agenda as $index => $item) {
                $multipart[] = ['name' => "agenda[$index]", 'contents' => $item];
            }
        }

        $activity = json_decode($request->input('activity'), true) ?? [];
        foreach ($activity as $index => $item) {
            $multipart[] = ['name' => "activity[$index]", 'contents' => $item];
        }

        $person = json_decode($request->input('person'), true) ?? [];
        foreach ($person as $index => $item) {
            $multipart[] = ['name' => "meet_of_person[$index]", 'contents' => $item];
        }

        $tags = json_decode($request->input('tags'), true) ?? [];
        foreach ($tags as $index => $item) {
            $multipart[] = ['name' => "tags[$index]", 'contents' => $item];
        }
        // return response()->json($multipart);

        // ✅ Handle new files
        if ($request->hasFile('new_files')) {
            foreach ($request->file('new_files') as $index => $file) {
                $multipart[] = [
                    'name' => "files[$index]",
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }
        }

        // ✅ Handle updated files and replacement paths
        $updateFiles = $request->file('update_files');
        $replacePaths = $request->input('replace_paths');
        $updateIndexes = $request->input('update_indexes');

        if (is_array($updateFiles)) {
            foreach ($updateFiles as $i => $file) {
                $index = $updateIndexes[$i] ?? 'unknown';
                $oldPath = $replacePaths[$i] ?? null;

                if ($oldPath) {
                    $multipart[] = ['name' => "replace_files[$index]", 'contents' => $oldPath];
                }

                $multipart[] = [
                    'name' => "files[$index]",
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }
        }

        // ✅ Handle removed files
        if ($request->has('remove_files')) {
            foreach ($request->input('remove_files') as $index => $path) {
                $multipart[] = ['name' => "remove_files[$index]", 'contents' => $path];
            }
        }

        try {
            $response = Http::withToken($accessToken)
                ->asMultipart()
                ->post(env('API_BASE_URL') . '/activity-docs/' . $id, $multipart);

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
                'message' => 'Document updated successfully.',
                'data' => $responseData
            ]);

        } catch (RequestException $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to send request to API.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $accessToken = session('user.access_token');

        $responseActivity = Http::withToken($accessToken)->get(env('API_BASE_URL')."/activities/{$id}");

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
