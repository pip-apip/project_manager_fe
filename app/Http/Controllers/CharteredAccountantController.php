<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
// Removed unused import
use Session;

class CharteredAccountantController extends Controller
{
    public function dataDummy()
    {
        $data = [
            [
                'id' => 1,
                'project_id' => 1,
                'tanggal_ca' => '2025-06-01',
                'nama_pemohon' => 'Andi Wijaya',
                'klasifikasi' => 'Audit',
                'keterangan' => 'Pemeriksaan laporan keuangan tahunan',
                'total_ca' => 15000000,
                'bukti_dokumen' => 'bukti_audit_andi.pdf',
            ],
            [
                'id' => 2,
                'project_id' => 2,
                'tanggal_ca' => '2025-06-05',
                'nama_pemohon' => 'Siti Nurhaliza',
                'klasifikasi' => 'Pajak',
                'keterangan' => 'Konsultasi perpajakan perusahaan',
                'total_ca' => 10000000,
                'bukti_dokumen' => 'bukti_pajak_siti.pdf',
            ],
            [
                'id' => 3,
                'project_id' => 3,
                'tanggal_ca' => '2025-06-10',
                'nama_pemohon' => 'Budi Santoso',
                'klasifikasi' => 'Akuntansi Manajemen',
                'keterangan' => 'Evaluasi anggaran semester',
                'total_ca' => 8000000,
                'bukti_dokumen' => 'laporan_budi.pdf',
            ],
            [
                'id' => 4,
                'project_id' => 4,
                'tanggal_ca' => '2025-06-15',
                'nama_pemohon' => 'Dewi Lestari',
                'klasifikasi' => 'Audit Internal',
                'keterangan' => 'Audit prosedur internal',
                'total_ca' => 12000000,
                'bukti_dokumen' => 'audit_internal_dewi.pdf',
            ],
            [
                'id' => 5,
                'project_id' => 5,
                'tanggal_ca' => '2025-06-18',
                'nama_pemohon' => 'Rizky Hidayat',
                'klasifikasi' => 'Forensik',
                'keterangan' => 'Investigasi keuangan',
                'total_ca' => 20000000,
                'bukti_dokumen' => 'investigasi_rizky.pdf',
            ],
        ];

        return response()->json([
            'status' => 200,
            'message' => 'Chartered accountant data retrieved successfully',
            'data' => $data,
            'errors' => []
        ], 200);
    }


    /**
     * Display a listing of the resource.
     */

    public function index(){
        $caData = $this->dataDummy()->getData(true)['data'];
        return view('pages.charteredAccountant.index', compact('caData'))->with([
            'title' => 'ca'
        ]);
    }
    public function indexCaProject(){

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

        return view('pages.charteredAccountant.indexProject', compact('results', 'users', 'groupedTeams'))->with([
            'title' => 'caProject'
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projectId = $request->query('project_id');

        $accessToken = session('user.access_token');
        $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects/search', [
            'limit' => 1000,
        ]);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $projects = $response->json()['data'];
        $ca = [];

        return view('pages.ca_pengajuan.form', compact('projects', 'projectId','ca'))->with(['title' => 'ca', 'status' => 'create']);
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
        $accessToken = session('user.access_token');
        $responseProject = Http::withToken($accessToken)->get(env('API_BASE_URL').'/projects/'.$id);

        if ($responseProject->failed()) {
            return redirect()->back()->withErrors('Failed to fetch project data.');
        }

        $project = $responseProject->json()['data'][0];

        // $responseActivity = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activities/search?project_id='.$id);

        // if ($responseActivity->failed()) {
        //     return redirect()->back()->withErrors('Failed to fetch activity data.');
        // }

        // $activities = $responseActivity->json()['data'];
        // return view('pages.charteredAccountant.byProject', compact('project', 'activities'))->with(['title' => 'caProject']);
        $caData = $this->dataDummy()->getData(true)['data'];
        return view('pages.charteredAccountant.byProject', compact('caData', 'project'))->with(['title' => 'caProject']);

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
