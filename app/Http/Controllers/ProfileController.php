<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{

    public function index()
    {
        return redirect()->route('profile.form');
    }

    public function form()
    {
        $accessToken = session('user.access_token');
        $id          = session('user.id');
        $response = Http::withToken($accessToken)->get(env('API_BASE_URL')."/users/{$id}");

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch user details.');
        }

        $user = $response->json()['data'][0];

        return view('pages.profile.form', compact('user'))->with(['title' => 'profile']);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/users/'.$id ,[
            'name' => $request['name'],
        ]);

        if ($response->successful()) {
            return redirect()->route('profile.form')->with('success', 'Data Profile edited successfully.');
        }
        $errorMessage = $response->json('message', 'Data Profile failed to edited. Please try again.');
        return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
    }

    public function password()
    {
        $accessToken = session('user.access_token');
        $id          = session('user.id');
        $response = Http::withToken($accessToken)->get(env('API_BASE_URL')."/users/{$id}");

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch user details.');
        }

        $user = $response->json()['data'][0];

        return view('pages.profile.password', compact('user'))->with(['title' => 'password']);
    }

    public function change(Request $request, string $id)
    {
        $request->validate([
            'password'      => 'required|string|min:8',
            'password_new'  => 'required|string|min:8',
        ]);

        $accessToken = session('user.access_token');

        $response = Http::withToken($accessToken)->patch(env('API_BASE_URL').'/users/'.$id ,[
            'old_password'          => $request['password'],
            'new_password'          => $request['password_new'],
            'confirm_new_password'  => $request['password_new'],
        ]);

        if ($response->successful()) {
            return redirect()->route('profile.password')->with('success', 'Data Password edited successfully.');
        }
        $errorMessage = $response->json('message', 'Data Password failed to edited. Please try again.');
        return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
    }
}
