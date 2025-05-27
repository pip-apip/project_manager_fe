<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class MediaLibraryController extends Controller
{
 /**
  * Create a new controller instance.
  *
  * @return void
  */
 public function __construct()
 {
   //$this->middleware(['auth', 'verified']);
 }

 /**
  * Get Media Library page
  * @return View
  */
 public function mediaLibrary(Request $request){
   $user_obj = 999;//array('id' => 1, 'name' => 'Ladur Cobain');//auth()->user();
   return view('medialibrary', ['user_obj' => $user_obj , 'title' => 'project']);
 }
}

