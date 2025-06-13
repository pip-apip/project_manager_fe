<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{

    public function index()
    {
        $accessToken = session('user.access_token');
        $response = '';

        if(session('user.role') == 'SUPERADMIN'){
            $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-docs?limit=1000');
        } else {
            $project_id = "";
            for($i = 0; $i < count(session('user.project_id')); $i++){
                if($i == 0){
                    $project_id = session('user.project_id')[$i];
                } else {
                    $project_id .= ",".session('user.project_id')[$i];
                }
            }

            $response = Http::withToken($accessToken)->get(env('API_BASE_URL').'/activity-docs/search?project_id='.$project_id.'&limit=1000');
        }

        if ($response->failed()) {
            return redirect()->back()->withErrors('Failed to fetch activity doc list.');
        }

        $activityDoc = $response->json()['data'];

        return view('pages.search', compact('activityDoc'))->with(['title' => 'Search']);
    }
}
