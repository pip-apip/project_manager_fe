@extends('layouts.app')

@section('title', 'Company Page')

@section('content')

<style>
    /* File Upload Wrapper */
    .file-upload-wrapper {
        background-color: #f4f3f2;
        padding: 20px;
        border-radius: 8px;
        border: none;
        text-align: center;
        width: 100%;
        color: #555;
        font-size: 16px;
        position: relative;
        cursor: pointer;
    }

    .file-upload-wrapper input[type="file"] {
        display: none;
    }

    .file-upload-wrapper .file-name {
        font-size: 14px;
        color: #555;
    }

    .file-upload-wrapper .browse {
        text-decoration: underline;
        font-weight: 500;
        color: #444;
    }

    .file-upload-wrapper.is-invalid {
        border: 1px solid #dc3545;
        background-color: #f8d7da;
        color: #721c24;
    }

    .file-upload-wrapper .file-upload{
        cursor: pointer;
    }

    .file-preview {
        margin-top: 10px;
        text-align: center;
    }

    .file-preview img {
        max-width: 200px;
        max-height: 150px;
        display: block;
        margin: 10px auto;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .remove-btn {
        background: none;
        border: none;
        color: #c00;
        cursor: pointer;
        text-decoration: underline;
        font-size: 14px;
        margin-top: 5px;
    }
</style>

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>{{ $status === 'create' ? 'Tambah' : 'Ubah' }} <span class="d-none d-md-inline-block">Perusahaan</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('company.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($status === 'create')
                        <form id="companyForm" action="{{ route('company.store') }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @elseif ($status === 'edit')
                        <form id="companyForm" action="{{ route('company.update', $company['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @endif
                    @csrf
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label>Nama Perusahaan <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Nama Perusahaan" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company ? $company['name'] : '') }}" autocomplete="off" />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Alamat <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <textarea class="form-control @error('address') is-invalid @enderror" placeholder="Masukkan Alamat Perusahaan" id="address" name="address">{{ old('address', $company ? $company['address'] : '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label>Nama Direktur <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Nama Direktur" class="form-control @error('director_name') is-invalid @enderror" name="director_name" id="director_name" value="{{ old('director_name', $company ? $company['director_name'] : '') }}" autocomplete="off" />
                                @error('director_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Tanggal Akta Pendirian Perusahaan <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="date" placeholder="Masukkan Tanggal Akta Pendirian Perusahaan" class="form-control @error('established_date') is-invalid @enderror" name="established_date" id="established_date" value="{{ old('established_date', $company ? $company['established_date'] : '') }}" autocomplete="off" />
                                @error('established_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Tanda Tangan Direktur</label>
                            </div>
                            <div class="col-md-10">
                                <div class="file-upload-wrapper {{ $errors->has('director_signature') ? 'is-invalid' : '' }}" id="dropzone">
                                    <label for="file-upload" class="file-upload">
                                        <div class="text" id="text"> Drag & Drop your files or <span class="browse">Browse</span></div>
                                        <input type="file" id="file-upload" name="director_signature" accept="image/*" />
                                        <div class="file-name" id="file-name"></div>
                                    </label>
                                    <div class="file-preview" id="file-preview">
                                        @if (!empty($company['director_signature']))
                                            <img src="{{ $API_url . $company['director_signature'] }}" id="preview-img">
                                            <button type="button" class="remove-btn" onclick="removeFile()"><i class="fa-solid fa-xmark"></i></button>
                                            <input type="hidden" name="existing_signature" value="{{ $company['director_signature'] }}">
                                        @endif
                                    </div>
                                </div>
                                @error('director_signature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                <button type="submit"
                                    class="btn btn-primary me-1 mb-1" id="submitButton">Simpan</button>
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
        $('form').on('submit', function() {
            // $('#fullPageLoader').show();
            buttonLoadingStart('submitButton');
        });
    });
</script>

{{-- File Upload Script --}}
<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-upload');
    const fileName = document.getElementById('file-name');

    // Handle file drag events
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        fileInput.files = files; // simulate file selection
        displayFileName(files);
    });

    // Handle file selection from the input
    fileInput.addEventListener('change', function(e) {
        displayFileName(fileInput.files);
    });

    // Function to display file name
    function displayFileName(files) {
        if (files.length > 0) {
            fileName.textContent = `Selected file: ${files[0].name}`;
        } else {
            fileName.textContent = '';
        }
        $('.text').hide();
    }

    const previewContainer = document.getElementById('file-preview');

    function displayPreviewImage(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewContainer.innerHTML = `
                <img src="${e.target.result}" id="preview-img">
                <button type="button" class="remove-btn" onclick="removeFile()"><i class="fa-solid fa-xmark"></i></button>
            `;
        };
        reader.readAsDataURL(file);
    }

    function removeFile() {
        fileInput.value = ''; // Clear the file input
        fileName.textContent = '';
        previewContainer.innerHTML = '';
        $('.text').show();
    }

    // Hook into file change to show preview
    fileInput.addEventListener('change', function(e) {
        if (fileInput.files && fileInput.files[0]) {
            displayFileName(fileInput.files);
            displayPreviewImage(fileInput.files[0]);
        }
    });

    // Also handle file drop
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        fileInput.files = files;
        if (files.length > 0) {
            displayFileName(files);
            displayPreviewImage(files[0]);
        }
    });
</script>

@endsection