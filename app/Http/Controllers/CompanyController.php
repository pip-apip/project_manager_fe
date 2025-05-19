<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;

class CompanyController extends Controller
{
    protected $API_url = "https://bepm.hanatekindo.com";

    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */

    public function index(){
        if (!request()->has('search')) {
            session()->forget('q');
        }
        $q = Session::get('q');
        $data['q'] = $q;

        $page = request('page', 1);
        $perPage = request()->has('per_page') ? request('per_page') : 10;

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/companies/search', [
            'name' => $q,
            'limit' => $perPage,
            'page' => $page
        ]);

        if ($response->failed()) {
            Log::error('Company search API failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return redirect()->back()->withErrors('Failed to fetch companies data.');
        }

        $total = $response->json()['pagination']['total'] ?? null;
        $companies = is_array($response->json()['data'] ?? null) ? $response->json()['data'] : null;
        $result;

        if (!is_array($companies) || empty($companies)) {
            $results = null;
        }else{
            $results = new LengthAwarePaginator(
                collect($companies),
                $total,
                $perPage,
                $page,
                ['path' => url('company')]
            );
        }


        return view('pages.company.index', $data, compact('results'))->with([
            'title' => 'company',
            'API_url' => $this->API_url
        ]);
    }

    public function filter(Request $request)
    {
        $q = $request->input('q', '');
        session(['q' => $q]);

        return redirect()->route('company.index', ['search' => $q]);
    }

    public function reset()
    {
        session()->forget('q');
        return redirect()->route('company.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = [];
        return view('pages.company.form', compact('company'))->with(['title' => 'company', 'status' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:100',
        //     'address' => 'required|string',
        //     'director_name' => 'required|string|max:100',
        //     'director_signature' => 'sometimes|mimes:jpeg,png,jpg|max:2048'
        // ]);

        $accessToken = session('user.access_token');
        $file = $request->file('director_signature');

        // Prepare the data
        $data = [
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'director_name' => $request->input('director_name'),
            'established_date' => $request->input('established_date'),
        ];

        if ($file) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $accessToken,
            ])
            ->attach('director_signature', file_get_contents($file), $file->getClientOriginalName())
            ->post('https://bepm.hanatekindo.com/api/v1/companies', $data);
        } else {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '. $accessToken,
            ])
            ->post('https://bepm.hanatekindo.com/api/v1/companies', $data);
        }

        if ($response->json()['status'] !== 201) {
            $errors = $response->json()['errors'];

            // Return the errors to the view, keeping old input data
            return redirect()->back()->withInput()->withErrors($errors);
        }

        return redirect()->route('company.index')->with('success', 'Company created successfully.');
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

        $response = Http::withToken($accessToken)->get("https://bepm.hanatekindo.com/api/v1/companies/{$id}");

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch company details.');
        }

        $company = $response->json()['data'][0];

        return view('pages.company.form', compact('company'))->with([
            'title' => 'company',
            'status' => 'edit',
            'API_url' => $this->API_url]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string',
            'director_name' => 'required|string|max:100',
            'director_phone' => 'required|string|max:20',
            'director_signature' => 'sometimes|mimes:jpeg,png,jpg|max:2048',
        ]);

        $accessToken = session('user.access_token');
        $file = $request->file('director_signature');

        $requestHttp = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ]);

        if ($file) {
            // If file exists, send multipart form data including the file
            $response = $requestHttp
                ->attach('director_signature', file_get_contents($file), $file->getClientOriginalName())
                ->asMultipart()
                ->post("https://bepm.hanatekindo.com/api/v1/companies/{$id}", [
                    ['name' => 'name', 'contents' => $validated['name']],
                    ['name' => 'address', 'contents' => $validated['address']],
                    ['name' => 'director_name', 'contents' => $validated['director_name']],
                    ['name' => 'director_phone', 'contents' => $validated['director_phone']],
                ]);
        } else {
            // If no file, just send JSON payload
            $response = $requestHttp
                ->post("https://bepm.hanatekindo.com/api/v1/companies/{$id}", $validated);
                // dd($response->json());
        }

        if ($response->json()['status'] == 400) {
            $errors = $response->json()['errors'];
            return redirect()->back()->withInput()->withErrors($errors);
        }
        return redirect()->route('company.index')->with('success', 'Company updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->delete('https://bepm.hanatekindo.com/api/v1/companies/'.$id);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to delete company.');
        }

        return redirect()->route('company.index')->with('success', 'Company delete successfully.');
    }
}
