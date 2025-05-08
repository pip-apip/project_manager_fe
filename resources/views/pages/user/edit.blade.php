@extends('layouts.app')

@section('title', 'Company Page')

@section('content')

<style>
    /* Style the tab buttons */
    .tablink {
        background-color: #fff;
        color: black;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        font-size: 17px;
    }
    .tablink.active {
        background-color: #eee;
        color: black;
    }

    .tablink:hover {
        background-color: #ddd;
    }

    /* Style the tab content (hidden by default) */
    .tabcontent {
        display: none;
        padding: 20px;
    }
</style>

<script>
    function openTab(evt, tabName) {
        let i, tabcontent, tablinks;

        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
// Open the first tab by default
//document.getElementsByClassName("tablink")[0].click();
</script>

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Ubah <span class="d-none d-md-inline-block">Pengguna</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('user.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-12">
                            <button class="tablink" onclick="openTab(event, 'Tab1')">Info. Umum</button>
                            <button class="tablink" onclick="openTab(event, 'Tab2')">Kata Sandi</button>
                        </div>
                        <hr>
                        <div class="col-sm-12 col-12">
                            <div id="Tab1" class="tabcontent" style="display:block;">
                                <form id="formEdit" action="{{ route('user.update', $user['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Username</label>
                                        </div>
                                        <div class="form-group col-md-10">
                                            <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user ? $user['username'] : '') }}" readonly />
                                        </div>
                                        <div class="col-md-2">
                                            <label>Nama Lengkap <code>*</code></label>
                                        </div>
                                        <div class="form-group col-md-10">
                                            <input type="text" placeholder="Masukkan Nama" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user ? $user['name'] : '') }}" autocomplete="off" />
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label>Hak Akses <code>*</code></label>
                                        </div>
                                        <fieldset class="form-group col-md-10">
                                            <select class="form-select" name="role" id="role">
                                                <option value="USER" {{ old('role', $user ? $user['role'] : '') == 'USER' ? 'selected' : '' }}>User</option>
                                                <option value="ADMIN" {{ old('role', $user ? $user['role'] : '') == 'ADMIN' ? 'selected' : '' }}>Admin</option>
                                                <option value="SUPERADMIN" {{ old('role', $user ? $user['role'] : '') == 'SUPERADMIN' ? 'selected' : '' }}>Super Admin</option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">
                                                    <i class="bx bx-radio-circle"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </fieldset>
                                        <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                            <button type="submit"
                                                class="btn btn-primary me-1 mb-1" id="submitButtonEdit">Simpan</button>
                                            <button type="reset"
                                                class="btn btn-light-secondary me-1 mb-1">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div id="Tab2" class="tabcontent" style="display:none;">
                                <form id="formPassword" action="{{ route('user.update', $user['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Username</label>
                                        </div>
                                        <div class="form-group col-md-10">
                                            <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user ? $user['username'] : '') }}" readonly />
                                        </div>
                                        <div class="col-md-2">
                                            <label>Password Lama <code>*</code></label>
                                        </div>
                                        <div class="form-group col-md-10">
                                            <input type="password" placeholder="Masukkan Password" class="form-control @error('old_password') is-invalid @enderror" id="old_password" name="old_password" autocomplete="off" />
                                            @error('old_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label>Password Baru <code>*</code></label>
                                        </div>
                                        <div class="form-group col-md-10">
                                            <input type="password" placeholder="Masukkan Password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" autocomplete="off" />
                                            @error('new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <label>Password Konfirm <code>*</code></label>
                                        </div>
                                        <div class="form-group col-md-10">
                                            <input type="password" placeholder="Masukkan Password" class="form-control @error('confirm_new_password') is-invalid @enderror" id="confirm_new_password" name="confirm_new_password" autocomplete="off" />
                                            @error('confirm_new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                            <button type="submit"
                                                class="btn btn-primary me-1 mb-1" id="submitButtonPassword">Simpan</button>
                                            <button type="reset"
                                                class="btn btn-light-secondary me-1 mb-1">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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

<script>
    $(document).ready(function() {
        $('#formEdit').on('submit', function() {
            // $('#fullPageLoader').show();
            buttonLoadingStart('submitButtonEdit');
        });
        $('#formPassword').on('submit', function() {
            // $('#fullPageLoader').show();
            buttonLoadingStart('submitButtonPassword');
        });

        const firstTab = document.querySelector('.tablink');
        if (firstTab) {
            firstTab.classList.add('active');
        }
    });
</script>

@endsection