<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function __construct(){
        //
    }

    public function index(){

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

        $responseProject = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects/search', $params);

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activities.');
        }

        $total = $responseProject->json()['pagination']['total'] ?? null;
        $projects = $responseProject->json()['data'] ?? null;

        $responseUser = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/users?limit=1000');

        if ($responseUser->failed()) {
            return redirect()->back()->withErrors('Failed to fetch user data.');
        }

        $users = $responseUser->json()['data'] ?? null;

        $responseTeam = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/project-teams?limit=1000');

        if ($responseTeam->failed()) {
            return redirect()->back()->withErrors('Failed to fetch user data.');
        }

        $teams = $responseTeam->json()['data'] ?? null;

        $groupedTeams = [];

        // Loop through each team and group by project_id
        foreach ($teams as $team) {
            $project_id = $team['project_id'];  // Get the project_id of each team

            // If the project_id is not already in the grouped array, add it
            if (!isset($groupedTeams[$project_id])) {
                $groupedTeams[$project_id] = [
                    'project_id' => $project_id,
                    'project_name' => $team['project_name'],  // Add project_name to the group
                    'members' => []  // Initialize an empty array for members
                ];
            }

            // Add team member to the corresponding project
            $groupedTeams[$project_id]['members'][] = [
                'id' => $team['user_id'],  // User ID
                'name' => $team['user_name']  // User Name
            ];
        }

        // Reindex the array to start from index 0
        $groupedTeams = array_values($groupedTeams);

        $results = '';
        if (!is_array($projects) || empty($projects)) {
            $results = [];
        }else{
            $results = new LengthAwarePaginator(
                collect($projects),
                $total,
                $perPage,
                $page,
                ['path' => url('project')]
            );
        }

        return view('pages.project.index', compact('results', 'users', 'groupedTeams'))->with([
            'title' => 'project'
        ]);
    }

    public function filter(Request $request)
    {
        $q = $request->input('q', '');
        $start_date = $request->input('start_date', '');
        $end_date = $request->input('end_date', '');

        session(['start_date' => $start_date]);
        session(['end_date' => $end_date]);
        session(['q' => $q]);

        return redirect()->route('project.index', ['search' => $q, 'start_date' => $start_date, 'end_date' => $end_date]);
    }

    public function reset()
    {
        session()->forget('q');
        session()->forget('start_date');
        session()->forget('end_date');
        return redirect()->route('project.index');
    }

    public function activity_project(string $id)
    {
        // dd(session('lastRoute'));
        $accessToken = session('user.access_token');
        $responseProject = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects/'.$id);

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $project = $responseProject->json()['data'][0];

        $responseActivity = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/activities/search?project_id='.$id);

        if ($responseActivity->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activity data.');
        }

        $activities = $responseActivity->json()['data'];

        return view('pages.project.activity', compact('project', 'activities'))->with(['title' => 'project']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $accessToken = session('user.access_token');
        $responseCompanies = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/companies');

        if ($responseCompanies->json()['status'] !== 201) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $companies = $responseCompanies->json()['data'];

        $responseUser = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/users');

        if ($responseUser->json()['status'] !== 201) {
            return redirect()->back()->withErrors('Failed to fetch user data.');
        }

        $users = $responseUser->json()['data'] ?? null;

        $project = [];
        return view('pages.project.form', compact('project', 'companies', 'users'))->with(['title' => 'project', 'status' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'company_id' => ['required', 'not_in:#'],
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date',
        // ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->post('https://bepm.hanatekindo.com/api/v1/projects', [
            'name' => $request->input('name'),
            'company_id' => $request->input('company_id'),
            'start_date' => date('Y-m-d', strtotime($request->input('start_date'))),
            'end_date' => date('Y-m-d', strtotime($request->input('end_date'))),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'project_leader_id' => $request->input('project_leader_id'),
        ]);

        // dd($response->json());

        if ($response->json()['status'] == 400) {
            $errors = $response->json()['errors'];

            // Return the errors to the view, keeping old input data
            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('project.index')->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for creating a new resource doc.
     */
    public function storeDoc(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'file' => 'required|file|mimes:pdf|max:2048',
            'project_id' => 'required',
            'admin_doc_category_id' => 'required',
        ]);

        $accessToken = session('user.access_token');
        $file = $request->file('file');
        // dd($file);

        // Prepare the data
        $data = [
            'title' => $request->input('title'),
            'project_id' => $request->input('project_id'),
            'admin_doc_category_id' => $request->input('admin_doc_category_id'),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '. $accessToken,
        ])
        ->attach('file', file_get_contents($file), $file->getClientOriginalName())
        ->post('https://bepm.hanatekindo.com/api/v1/admin-docs', $data);

        // dd($response->json());

        if ($response->json()['status'] == 400) {
            $errors = $response->json()['errors'];

            // Return the errors to the view, keeping old input data
            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('project.doc', ['id' => $request->input('project_id')])->with('success', 'Project Doc created successfully.');
    }

    public function storeTeam(){
        $teams= request('team');
        $project_id = request('project_id');

        $accessToken = session('user.access_token');

        $responseDelete = Http::withToken($accessToken)->delete('https://bepm.hanatekindo.com/api/v1/project-teams/'.$project_id);
        if ($responseDelete->json()['status'] == 400 || $responseDelete->json()['status'] == 200) {

            foreach ($teams as $key => $team) {
                $response = Http::withToken($accessToken)->post('https://bepm.hanatekindo.com/api/v1/project-teams', [
                    'user_id' => $team['id'],
                    'project_id' => $project_id
                ]);

                if ($response->json()['status'] == 400 || $response->json()['status'] == 500) {
                    $errors = $response->json()['errors'];

                    return response()->json([
                        'status' => 'error',
                        'message' => $errors
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Teams Added successfully.'
            ]);

        }else{
            $errors = $responseDelete->json()['errors'];

            return response()->json([
                'status' => 'error',
                'message' => $errors
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $accessToken = session('user.access_token');
        $responseProject = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/projects/'.$id);

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $responseDocProject = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/admin-docs/search?project_id='.$id.'&limit=1000');
        // dd($responseDocProject->json());

        if ($responseDocProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch doc project data.');
        }

        $responseCategoryDocAdmin = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/admin-doc-categories');

        if ($responseCategoryDocAdmin->failed()) {
            return redirect()->back()->withErrors('Failed to fetch doc category of administration project data.');
        }

        $data = [
            'project'       => $responseProject->json()['data'][0],
            'docProject'    => $responseDocProject->json()['data'],
            'categoryDoc'   => $responseCategoryDocAdmin->json()['data']
        ];

        // dd($data);

        return view('pages.project.doc', compact('data'))->with(['title' => 'project']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $accessToken = session('user.access_token');

        $responseProject = Http::withToken($accessToken)->get("https://bepm.hanatekindo.com/api/v1/projects/{$id}");

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch category details.');
        }

        $project = $responseProject->json()['data'][0];

        $responseCompanies = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/companies');

        if ($responseCompanies->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $responseUser = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/users');

        if ($responseUser->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch user data.');
        }

        $users = $responseUser->json()['data'] ?? null;
        $companies = $responseCompanies->json()['data'];

        // dd($project);

        return view('pages.project.form', compact('project', 'companies', 'users'))->with(['title' => 'project', 'status' => 'edit']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => ['required', 'not_in:#'],
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->patch('https://bepm.hanatekindo.com/api/v1/projects/'. $id, [
            'name' => $request->input('name'),
            'company_id' => $request->input('company_id'),
            'start_date' => date('Y-m-d', strtotime($request->input('start_date'))),
            'end_date' => date('Y-m-d', strtotime($request->input('end_date'))),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);

        if ($response->json()['status'] == 400) {
            $errors = $response->json()['errors'];

            // Return the errors to the view, keeping old input data
            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('project.index')->with('success', 'Project edited successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->delete('https://bepm.hanatekindo.com/api/v1/projects/'.$id);

        if ($response->json()['status'] == 400 || $response->json()['status'] == 500) {
            $errors = $response->json()['errors'];

            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Data Proyek Berhasil di Hapus');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyDoc(string $id)
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->delete('https://bepm.hanatekindo.com/api/v1/admin-docs/'.$id);

        if ($response->json()['status'] == 400 || $response->json()['status'] == 500) {
            $errors = $response->json()['errors'];

            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Dokumen Proyek Berhasil di Hapus.');
    }
}
