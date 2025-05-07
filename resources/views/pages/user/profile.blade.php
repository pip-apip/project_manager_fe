@extends('layouts.app')

@section('title', 'Home Page')

@section('content')

<div class="page-heading">
    {{-- <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Account Profile</h3>
                <p class="text-subtitle text-muted">A page where users can change profile information</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div> --}}
    <section class="section">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center flex-column">
                            <div class="avatar avatar-2xl">
                                <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="Avatar" style="width: 150px; height: 150px;">
                            </div>

                            <h3 class="mt-3">{{ $user['name'] }}</h3>
                            <p class="text-small">{{ $user['role'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8" id="view">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-8 col-8">
                                <h1>{{ $status }} <span class="d-none d-md-inline-block">Pengguna</span></h1>
                            </div>
                            <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                                <a href="{{ route('user.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="#" method="get">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Username : </label>
                                </div>
                                <div class="col-md-10 form-group">
                                    <input type="text" placeholder="Masukkan Username" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user ? $user['username'] : '') }}" disabled>
                                </div>

                                <div class="col-md-2">
                                    <label>Nama : </label>
                                </div>
                                <div class="col-md-10 form-group">
                                    <input type="text" placeholder="Masukkan Username" class="form-control @error('nama') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user ? $user['name'] : '') }}" disabled>
                                </div>
                                <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                    <button type="submit" class="btn btn-primary me-1 mb-1">Edit</button>
                                    <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8" id="edit" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-8 col-8">
                                <h1>{{ $status }} <span class="d-none d-md-inline-block">Pengguna</span></h1>
                            </div>
                            <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                                <a href="{{ route('user.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="#" method="get">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Username : </label>
                                </div>
                                <div class="col-md-10 form-group">
                                    <input type="text" placeholder="Masukkan Username" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user ? $user['username'] : '') }}" disabled>
                                </div>

                                <div class="col-md-2">
                                    <label>Nama : </label>
                                </div>
                                <div class="col-md-10 form-group">
                                    <input type="text" placeholder="Masukkan Username" class="form-control @error('nama') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user ? $user['name'] : '') }}" disabled>
                                </div>
                                <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                    <button type="submit" class="btn btn-primary me-1 mb-1">Edit</button>
                                    <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    console.log({!! json_encode($user) !!})
</script>

@endsection