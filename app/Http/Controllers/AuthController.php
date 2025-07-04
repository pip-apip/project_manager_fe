<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    public function login(){
        if(session('user')){
            return redirect()->route('home');
        }
        return view('auth.login', ['session' => session()->all()]);
    }

    public function doLogin(){

        $validatedData = request()->validate([
            'username'  => 'required|string|max:255',
            'password'  => 'required|string|min:8',
        ]);

        $response = Http::post(env('API_BASE_URL').'/auth/login', [
            'username'  => $validatedData['username'],
            'password'  => $validatedData['password'],
        ]);


        if ($response->json('status') === 200) {

            session([
                'user' => [
                    'access_token'  => $response->json('data.access_token'),
                    'refresh_token' => $response->json('data.refresh_token'),
                    'name'          => $response->json('data.name'),
                    'username'      => $response->json('data.username'),
                    'role'          => $response->json('data.role'),
                    'id'            => $response->json('data.id'),
                ],
                'lastRoute' => 'home',
                'currentRoute' => 'home',
            ]);

            $responseTeams =Http::withToken($response->json('data.access_token'))->get(env('API_BASE_URL').'/project-teams/search?user_id='.$response->json('data.id').'&limit=1000');

            if($responseTeams->json('data')){
                $teams = $responseTeams->json('data');
                $teamIds = [];
                foreach ($teams as $team) {
                    $teamIds[] = $team['project_id'];
                }

                session(['user.project_id' => $teamIds]);
            } else {
                session(['user.project_id' => []]);
            }

            $responsePL = Http::withToken($response->json('data.access_token'))->get(env('API_BASE_URL').'/projects/search?project_leader_id='.$response->json('data.id').'&limit=1000');
            if($responsePL->json('data')){
                $projects = $responsePL->json('data');
                $projectIds = [];
                foreach ($projects as $project) {
                    $projectIds[] = $project['id'];
                }
                session(['user.project_leader' => true]);
                session(['user.project_id' => array_merge(session('user.project_id'), $projectIds)]);
                session(['user.project_leader_id' => $projectIds]);
            } else {
                session(['user.project_leader' => false]);
                session(['user.project_id' => []]);
                session(['user.project_leader_id' => []] );
            }

            return redirect()->route('home')->with('success', 'Login successful.');

        } elseif ($response->json('status') === 401) {
            $errorMessage = 'Invalid username or password.';
            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
        } else {
            $errorMessage = $response->json('message', 'Login failed. Please try again.');
            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    public function register()
    {
        return view('auth.register');
    }

    public function doRegister(Request $request)
    {

        $request->validate([
            'name'      => 'required|string|max:255',
            'user_name'  => 'required|string|max:255',
            'user_password'  => 'required|string|min:8',
        ]);

        $response = Http::post(env('API_BASE_URL').'/auth/register', [
            'name'      => $request['name'],
            'username'  => $request['user_name'],
            'password'  => $request['user_password'],
        ]);

        if ($response->successful()) {
            return redirect()->route('user.index')->with('success', 'Data User created successfully.');
        }

        $errorMessage = $response->json('message', 'Data User failed to create. Please try again.');
        return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
    }

    public function logout(){

        $token = session('user.access_token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'You are not logged in.');
        }

        $response = Http::withToken($token)->post(env('API_BASE_URL').'/auth/logout');

        session()->forget(['user']);
        session()->flush();

        if ($response->successful()) {
            return redirect()->route('login')->with('success', 'Logged out successfully.');
        }

        return redirect()->route('login')->with('error', 'Logout failed. Please try again.');
    }
}
