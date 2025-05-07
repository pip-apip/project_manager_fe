<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ErrController extends Controller
{

    public function index()
    {
        return view('pages.err')->with(['title' => 'Error']);
    }
}
