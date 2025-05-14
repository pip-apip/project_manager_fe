<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryAdmController;
use App\Http\Controllers\CategoryActController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ErrController;

use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\RefershTokenMiddleware;
use App\Http\Middleware\MaintenanceRedirect;
use App\Http\Middleware\RoleMiddleware;


Route::get('/', function () {
    return redirect()->route('login');
    // return view('maintenance');
});

// Auth
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'doLogin'])->name('login.process');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::get('get-token', [AuthController::class, 'refreshAccessToken'])->name('get-token');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('err', [ErrController::class, 'index'])->name('err');

Route::middleware([AuthMiddleware::class, RefershTokenMiddleware::class])->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home')->middleware(['role:SUPERADMIN,ADMIN,USER']);

    Route::get('search', [SearchController::class, 'index'])->name('search.index');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/form', [ProfileController::class, 'form'])->name('profile.form');
    Route::post('profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/change/{id}', [ProfileController::class, 'change'])->name('profile.change');


    // Category Administration
    Route::get('categoryAdm', [CategoryAdmController::class, 'index'])->name('categoryAdm.index')->middleware(['role:SUPERADMIN,ADMIN']);
    Route::get('categoryAdm/form', [CategoryAdmController::class, 'create'])->name('categoryAdm.create');
    Route::post('categoryAdm/store', [CategoryAdmController::class, 'store'])->name('categoryAdm.store');
    Route::get('categoryAdm/form-edit/{id}', [CategoryAdmController::class, 'edit'])->name('categoryAdm.edit');
    Route::post('categoryAdm/update/{id}', [CategoryAdmController::class, 'update'])->name('categoryAdm.update');
    Route::get('categoryAdm/delete/{id}', [CategoryAdmController::class, 'destroy'])->name('categoryAdm.destroy');

    Route::post('categoryAdm/filter', [CategoryAdmController::class, 'filter'])->name('categoryAdm.filter');
    Route::get('categoryAdm/reset', [CategoryAdmController::class, 'reset'])->name('categoryAdm.reset');

    // Category Activity
    Route::get('categoryAct', [CategoryActController::class, 'index'])->name('categoryAct.index');
    Route::get('categoryAct/form', [CategoryActController::class, 'create'])->name('categoryAct.create');
    Route::post('categoryAct/store', [CategoryActController::class, 'store'])->name('categoryAct.store');
    Route::get('categoryAct/form-edit/{id}', [CategoryActController::class, 'edit'])->name('categoryAct.edit');
    Route::post('categoryAct/update/{id}', [CategoryActController::class, 'update'])->name('categoryAct.update');
    Route::get('categoryAct/delete/{id}', [CategoryActController::class, 'destroy'])->name('categoryAct.destroy');

    Route::post('categoryAct/filter', [CategoryActController::class, 'filter'])->name('categoryAct.filter');
    Route::get('categoryAct/reset', [CategoryActController::class, 'reset'])->name('categoryAct.reset');

    // Company
    Route::get('company', [CompanyController::class, 'index'])->name('company.index');
    Route::get('company/form', [CompanyController::class, 'create'])->name('company.create');
    Route::post('company/store', [CompanyController::class, 'store'])->name('company.store');
    Route::get('company/form-edit/{id}', [CompanyController::class, 'edit'])->name('company.edit');
    Route::post('company/update/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::get('company/delete/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');

    Route::post('company/filter', [CompanyController::class, 'filter'])->name('company.filter');
    Route::get('company/reset', [CompanyController::class, 'reset'])->name('company.reset');

    // Project
    Route::get('project', [ProjectController::class, 'index'])->name('project.index');
    Route::get('project/form', [ProjectController::class, 'create'])->name('project.create');
    Route::post('project/store', [ProjectController::class, 'store'])->name('project.store');
    Route::get('project/form-edit/{id}', [ProjectController::class, 'edit'])->name('project.edit');
    Route::post('project/update/{id}', [ProjectController::class, 'update'])->name('project.update');
    Route::get('project/destroy/{id}', [ProjectController::class, 'destroy'])->name('project.destroy');

    Route::get('project/doc/{id}', [ProjectController::class, 'show'])->name('project.doc');
    Route::post('project/storeDoc', [ProjectController::class, 'storeDoc'])->name('project.store.doc');
    Route::get('project/destroyDoc/{id}', [ProjectController::class, 'destroyDoc'])->name('project.destroy.doc');

    Route::get('project/activity/{id}', [ProjectController::class, 'activity_project'])->name('project.activity');

    Route::post('project/storeTeam', [ProjectController::class, 'storeTeam'])->name('project.store.team');

    Route::post('project/filter', [ProjectController::class, 'filter'])->name('project.filter');
    Route::get('project/reset', [ProjectController::class, 'reset'])->name('project.reset');

    // Activity
    Route::get('activity', [ActivityController::class, 'index'])->name('activity.index');
    Route::get('activity/form', [ActivityController::class, 'create'])->name('activity.create');
    Route::post('activity/form', [ActivityController::class, 'store'])->name('activity.store');
    Route::get('activity/form-edit/{id}', [ActivityController::class, 'edit'])->name('activity.edit');
    Route::post('activity/update/{id}', [ActivityController::class, 'update'])->name('activity.update');
    Route::get('activity/destroy/{id}', [ActivityController::class, 'destroy'])->name('activity.destroy');

    Route::get('activity/doc/{id}', [ActivityController::class, 'show'])->name('activity.doc');
    Route::post('activity/doc/store', [ActivityController::class, 'storeDoc'])->name('activity.doc.store');
    Route::get('activity/doc/delete/{id}', [ActivityController::class, 'destroyDoc'])->name('activity.doc.delete');

    Route::post('activity/filter', [ActivityController::class, 'filter'])->name('activity.filter');
    Route::get('activity/reset', [ActivityController::class, 'reset'])->name('activity.reset');

    // User
    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::get('user/form', [UserController::class, 'create'])->name('user.create');
    Route::post('user', [AuthController::class, 'doRegister'])->name('user.store');
    Route::get('user/form-edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('user/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::get('profile/{id}', [UserController::class, 'show'])->name('user.profile');

    Route::post('user/filter', [UserController::class, 'filter'])->name('user.filter');
    Route::get('user/reset', [UserController::class, 'reset'])->name('user.reset');

    // Progress
    Route::get('progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('progress/project/{id}', [ProgressController::class, 'show'])->name('progress.project');
    // Route::get('/activity-project/{id}', [ActivityController::class, 'activity_project'])->name('activity.project');
});

Route::get('test', function () {
    $title = 'Test';
    return view('pages.test', compact('title'));
})->name('test');

Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
});


// Route::post('project', [ProjectController::class, 'store'])->name('project.store');
// Route::patch('project/{id}', [ProjectController::class, 'update'])->name('project.update');

// Route::post('category', [CategoryController::class, 'store'])->name('category.store');
