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
            $params[session('user.role') == 'SUPERADMIN' ? 'name' : 'name'] = $q;
        }

        if (!empty($start_date)) {
            $params['start_date'] = date('Y-m-d', strtotime($start_date));
        }

        if (!empty($end_date)) {
            $params['end_date'] = date('Y-m-d', strtotime($end_date));
        }

        if (session('user.role') != 'SUPERADMIN') {
            $project_ids = session('user.project_id', []);
            $params['id'] = is_array($project_ids) ? implode(',', $project_ids) : $project_ids;
        }

        // $startTimeProject = microtime(true);
        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects/search', $params);
        // $endTimeProject = microtime(true);
        // $responseTimeProject = $endTimeProject - $startTimeProject;

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activities.');
        }

        $total = $responseProject->json()['pagination']['total'] ?? null;
        $projects = $responseProject->json()['data'] ?? null;

        // $startTimeUser = microtime(true);
        $responseUser = Http::withToken($accessToken)->get(env('API_BASE_URL').'/users?limit=1000');
        // $endTimeUser = microtime(true);
        // $responseTimeUser = $endTimeUser - $startTimeUser;

        if ($responseUser->failed()) {
            return redirect()->back()->withErrors('Failed to fetch user data.');
        }

        $users = $responseUser->json()['data'] ?? null;

        // $startTimeTeam = microtime(true);
        $responseTeam = Http::withToken($accessToken)->get(env('API_BASE_URL').'/project-teams?limit=1000');
        // $endTimeTeam = microtime(true);
        // $responseTimeTeam = $endTimeTeam - $startTimeTeam;

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

        // dd([
        //     'projects_response_time' => $responseTimeProject . ' seconds',
        //     'users_response_time' => $responseTimeUser . ' seconds',
        //     'teams_response_time' => $responseTimeTeam . ' seconds',
        //     'projects' => $projects,
        //     'users' => $users,
        //     'groupedTeams' => $groupedTeams
        // ]);

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
        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects/'.$id);

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $project = $responseProject->json()['data'][0];

        $responseActivity = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activities/search?project_id='.$id);

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
        $responseCompanies = Http::withToken($accessToken)->get(env('API_BASE_URL').'/companies?limit=1000');

        if ($responseCompanies->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $responseUser = Http::withToken($accessToken)->get(env('API_BASE_URL').'/users');

        if ($responseUser->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch user data.');
        }

        $responseUser = Http::withToken($accessToken)->get(env('API_BASE_URL').'/users?limit=1000');

        if ($responseUser->failed()) {
            return redirect()->back()->withErrors('Failed to fetch user data.');
        }

        $users = $responseUser->json()['data'] ?? null;

        $companies = $responseCompanies->json()['data'];
        $users = $responseUser->json()['data'] ?? null;
        $project = [];

        return view('pages.project.form', compact('project', 'companies', 'users'))->with(['title' => 'project', 'status' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'company_id' => ['required', 'not_in:#'],
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date',
        // ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->post(env('API_BASE_URL').'/projects', [
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'contract_number' => $request->input('contract_number'),
            'contract_date' => $request->input('contract_date'),
            'client' => $request->input('client'),
            'ppk' => $request->input('ppk'),
            'support_teams' => $request->input('support_teams'),
            'value' => $request->input('value'),
            'company_id' => $request->input('company_id'),
            'project_leader_id' => $request->input('project_leader_id'),
            'start_date' => date('Y-m-d', strtotime($request->input('start_date'))),
            'end_date' => date('Y-m-d', strtotime($request->input('end_date'))),
            'maintenance_date' => date('Y-m-d', strtotime($request->input('maintenance_date'))),
        ]);

        if ($response->json()['status'] !== 201) {
            $errors = $response->json()['errors'];
            dd($errors);
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $latestproject = $response->json()['data']['id'];

        $dataReq = new Request([
            'teams' => json_decode($request->input('teams'), true),
            'project_id' => $latestproject,
        ]);

        $returnAddTeams = $this->storeTeam($dataReq);
        // dd($returnAddTeams->getData());
        if ($returnAddTeams->getData()->status == 'error') {
            $errors = $returnAddTeams->json()['message'];
            dd($errors);
            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('project.index')->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for creating a new resource doc.
     */
    public function storeDoc(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|string|max:100',
        //     'file' => 'required|file|mimes:pdf|max:2048',
        //     'project_id' => 'required',
        //     'admin_doc_category_id' => 'required',
        // ]);

        $accessToken = session('user.access_token');
        // $file = $request->file('file');
        // dd($file);

        // Prepare the data
        $data = [
            'title' => $request->input('title'),
            'project_id' => $request->input('project_id'),
            'admin_doc_category_id' => $request->input('admin_doc_category_id'),
            'file' => $request->input('uploaded_file_name'),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '. $accessToken,
        ])
        // ->attach('file', file_get_contents($file), $file->getClientOriginalName())
        ->post(env('API_BASE_URL').'/admin-docs', $data);

        // dd($response->json());

        if ($response->json()['status'] !== 201) {
            $errors = $response->json()['errors'];

            // Return the errors to the view, keeping old input data
            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('project.doc', ['id' => $request->input('project_id')])->with('success', 'Project Doc created successfully.');
    }

    // public function storeTeam(){
    public function storeTeam(Request $request){

        $teams = $request->input('teams');
        $project_id = $request->input('project_id');

        if (empty($teams)) {
            $teams = request('teams');
        }
        if (empty($project_id)) {
            $project_id = request('project_id');
        }

        // return response()->json([
        //     'teams' => $teams,
        //     'project_id' => $project_id
        // ]);

        $accessToken = session('user.access_token');

        $responseDelete = Http::withToken($accessToken)->delete(env('API_BASE_URL').'/project-teams/'.$project_id);
        if ($responseDelete->json()['status'] == 400 || $responseDelete->json()['status'] == 200) {

            foreach ($teams as $key => $team) {
                $response = Http::withToken($accessToken)->post(env('API_BASE_URL').'/project-teams', [
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
        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects/'.$id);

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $responseDocProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/admin-docs/search?project_id='.$id.'&limit=1000');
        // dd($responseDocProject->json());

        if ($responseDocProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch doc project data.');
        }

        $responseCategoryDocAdmin = Http::withToken($accessToken)->get(env('API_BASE_URL').'/admin-doc-categories/search?limit=1000');

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

        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL')."/projects/{$id}");

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch category details.');
        }

        $project = $responseProject->json()['data'][0];

        $responseCompanies = Http::withToken($accessToken)->get(env('API_BASE_URL').'/companies?limit=1000');

        if ($responseCompanies->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $responseUser = Http::withToken($accessToken)->get(env('API_BASE_URL').'/users?limit=1000');

        if ($responseUser->json()['status'] !== 200) {
            return redirect()->back()->withErrors('Failed to fetch user data.');
        }

        $users = $responseUser->json()['data'] ?? null;
        $companies = $responseCompanies->json()['data'];

        // dd($project);

        return view('pages.project.form-edit', compact('project', 'companies', 'users'))->with(['title' => 'project', 'status' => 'edit']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'company_id' => ['required', 'not_in:#'],
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date',
        // ]);
        $data = [];

        foreach($request->all() as $key => $value){
            if($key !== '_token' && $key !== 'value' && strpos($key, '_date') === false && $key !== 'support_teams'){
                $data[$key] = $value;
            }elseif($key === 'value'){
                $data[$key] = str_replace('.', '', $value);
            }elseif(strpos($key, '_date') !== false){
                $data[$key] = date('Y-m-d', strtotime($value));
            }elseif($key === 'support_teams'){
                $data[$key] = json_decode($value, true);;
            }
        }

        // dd($data);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/projects/'. $id, $data);

        if ($response->json()['status'] == 400) {
            $errors = $response->json()['errors'];

            return redirect()->back()->withInput()->with('error',$errors);
        }

        return redirect()->route('project.index')->with('success', 'Project edited successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $accessToken = session('user.access_token');

        $responseDelete = Http::withToken($accessToken)->delete(env('API_BASE_URL').'/projects/'.$id);

        if ($responseDelete->json()['status'] !== 200) {
            $errors = $responseDelete->json()['errors'];

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

        $response = Http::withToken($accessToken)->delete(env('API_BASE_URL').'/admin-docs/'.$id);

        if ($response->json()['status'] == 400 || $response->json()['status'] == 500) {
            $errors = $response->json()['errors'];

            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Dokumen Proyek Berhasil di Hapus.');
    }
}
