@extends('layouts.app')

@section('title', 'Profile Page')

@section('content')

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Ubah <span class="d-none d-md-inline-block">Kata Sandi</span></h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                   <form action="{{ route('profile.change', $user['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <label>Username</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ old('username', $user ? $user['username'] : '') }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Hak Akses</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ old('role', $user ? $user['role'] : '') }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Password Lama <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="password" placeholder="Masukkan Password Lama" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Password Baru <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="password" placeholder="Masukkan Password Baru" class="form-control @error('password_new') is-invalid @enderror" id="password_new" name="password_new">
                                @error('password_new')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                <button type="submit"
                                    class="btn btn-primary me-1 mb-1">Simpan</button>
                                <button type="reset"
                                    class="btn btn-light-secondary me-1 mb-1">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<div id="fullPageLoader" class="full-page-loader" style="display: none">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div id="modernImageModal" class="modern-modal" style="display: none" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modern-modal-content">
        <img id="modernImagePreview" alt="Preview">
    </div>
    <span class="closeImage" onclick="closeModernModal()">&times;</span>
</div>

<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@if(session()->has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session()->get('success') }}',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            $('#fullPageLoader').hide();
        });
    </script>
@endif

@if(session()->has('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session()->get('error') }}',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            $('#fullPageLoader').hide();
        });
    </script>
@endif

@endsection