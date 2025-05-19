<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        //

    }
    public function index()
    {
        if (!request()->has('search')) {
            session()->forget('q');
        }
        $q = session('q');

        $page = request('page', 1);
        $perPage = request('per_page', 10);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->get('https://bepm.hanatekindo.com/api/v1/users/search', [
            'name' => $q,
            'limit' => $perPage,
            'page' => $page
        ]);

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch users.');
        }

        $total = $response->json()['pagination']['total'] ?? null;
        $users = is_array($response->json()['data'] ?? null) ? $response->json()['data'] : null;
        $results = null;

        if (!is_array($users) || empty($users)) {
            $results = null;
        }else{
            $results = new LengthAwarePaginator(
                collect($users),
                $total,
                $perPage,
                $page,
                ['path' => url('user')]
            );
        }

        return view('pages.user.index', compact('results', 'q'))->with(['title' => 'user']);
    }

    public function filter(Request $request)
    {
        $q = $request->input('q', '');
        session(['q' => $q]);

        return redirect()->route('user.index', ['search' => $q]);
    }

    public function reset()
    {
        session()->forget('q');
        return redirect()->route('user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = [];
        return view('pages.user.form', compact('user'))->with(['title' => 'user', 'status' => 'create']);
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

        $response = Http::withToken($accessToken)->get("https://bepm.hanatekindo.com/api/v1/users/{$id}");

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch category details.');
        }

        $user = $response->json()['data'][0];

        return view('pages.user.profile', compact('user'))->with(['title' => 'user', 'status' => 'Detail']);
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(String $id){
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->get("https://bepm.hanatekindo.com/api/v1/users/{$id}");

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch category details.');
        }

        $user = $response->json()['data'][0];

        return view('pages.user.edit', compact('user'))->with(['title' => 'user', 'status' => 'edit']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $request->validate([
        //     'name'      => 'required|string|max:255',
        //     'username'  => 'required|string|max:255',
        //     'password'  => 'required|string|min:8',
        // ]);

        $accessToken = session('user.access_token');
        $data = [];
        if($request['name']){
            $data['name'] = $request['name'];
        }
        if($request['username']){
            $data['username'] = $request['username'];
        }
        if($request['old_password']){
            $data['old_password'] = $request['old_password'];
        }
        if($request['new_password']){
            $data['new_password'] = $request['new_password'];
        }
        if($request['confirm_new_password']){
            $data['confirm_new_password'] = $request['confirm_new_password'];
        }

        $response = Http::withToken($accessToken)->patch('https://bepm.hanatekindo.com/api/v1/users/'.$id , $data);

        if ($response->json()['status'] === 200) {
            return redirect()->route('user.index')->with('success', 'Data User edited successfully.');
        }
        // $errorMessage = $response->json('message', 'Data User failed to edited. Please try again.');
        return redirect()->back()->with('error', $response->json()['errors']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->delete("https://bepm.hanatekindo.com/api/v1/users/{$id}");

        if ($response->successful()) {
            return redirect()->route('user.index')->with('success', 'Data User deleted successfully.');
        }
        $errorMessage = $response->json('message', 'Data User failed to deleted. Please try again.');
        return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
    }
}
