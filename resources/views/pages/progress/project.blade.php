@extends('layouts.app')

@section('title', 'Project Page')

@section('content')

<style>
    /* File Upload */
    .file-upload-wrapper {
        background-color: #f4f3f2;
        padding: 16px;
        border-radius: 12px;
        text-align: center;
        width: 100%;
        font-size: 16px;
        position: relative;
        color: #fff;
        cursor: pointer;
        border: none;
    }

    .file-upload-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px;
        background-color: #5f5b59;
        border-radius: 12px;
        position: relative;
        color: white;
        cursor: pointer;
    }

    .file-upload-wrapper input[type="file"] {
        display: none;
    }

    .upload-text {
        font-size: 14px;
        color: #fff;
    }

    .browse {
        text-decoration: underline;
        font-weight: 500;
        color: #fff;
    }

    .file-preview {
        display: flex;
        flex-direction: column;
        gap: 8px;
        background-color: #5f5b59;
        padding: 12px;
        border-radius: 10px;
        color: white;
        width: 100%;
        margin-top: 10px;
        font-size: 14px;
    }

    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #44403f;
        padding: 8px 12px;
        border-radius: 8px;
        transition: background-color 0.2s;
    }

    .file-item:hover {
        background-color: #524c4b;
    }

    .file-name {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        max-width: 80%;
    }

    .file-size {
        font-size: 12px;
        color: #ccc;
        margin-left: 6px;
    }

    .remove-file {
        background-color: #e74c3c;
        color: white;
        border-radius: 50%;
        padding: 2px 8px;
        font-weight: bold;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        transition: background-color 0.2s;
    }

    .remove-file:hover {
        background-color: #c0392b;
    }

    .file-upload-area.is-invalid {
        border: 2px dashed #e74c3c;
        background-color: #7b4a4a;
        color: #ffecec;
    }

    .file-upload-area.is-invalid .upload-text,
    .file-upload-area.is-invalid .browse {
        color: #ffecec;
    }

    .file-preview.is-invalid {
        border: 2px solid #e74c3c;
        background-color: #7b4a4a;
    }

    .file-error {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 2px;
        display: flex;
        flex-direction: row-reverse;
    }
</style>

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Progress <span class="d-none d-md-inline-block">Proyek</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('progress.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-striped" id="table">
                                <thead>
                                    <tr>
                                        <th>Spec Tech</th>
                                        <th width="15%" class="text-center">Progress</th>
                                        <th width="15%" class="text-center">Keterangan</th>
                                        <th width="5%" class="text-center">Gambar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activityCategory as $cat)
                                        <tr>
                                            <td>{{ $cat['name'] }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center">
                                                    <input type="text" class="form-control text-center" id="progress_{{ $cat['id'] }}" name="progress_{{ $cat['id'] }}" value="0">
                                                    <span class="ms-2">%</span>
                                                </div>
                                            </td>
                                            <td width="15%">
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="2"></textarea>
                                            </td>
                                            <td width="5%" class="text-center">
                                                <a href="http://" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#detailModal"><i class="fa-solid fa-image"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12 d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary me-1 mb-1" id="submitButtonPage1">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade text-left w-100" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Progress Image</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="closeDetailModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" id="form-content">
                            <div class="file-upload-wrapper" id="dropzone">
                                <label for="file-upload" class="file-upload-area @error('file') is-invalid @enderror">
                                    <div class="upload-text" id="upload-text">
                                        Drag & Drop your files or <span class="browse">Browse</span>
                                    </div>
                                    <input type="file" id="file-upload" name="file[]" multiple />
                                    <div class="file-preview" id="file-preview" style="display: none;"></div>
                                </label>
                            </div>

                            @error('file')
                            <small class="file-error-text" style="color: #e74c3c;" id="file-error">
                                {{ $message }}
                            </small>
                            @enderror
                        </div>
                        <div class="col-sm-12" id="show-content" style="display: none;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal Upload</th>
                                        <th class="text-center">File</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="file-list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-warning ml-1" id="editButton" style="display: none;">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger ml-1" id="saveButton">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    let status;
    document.addEventListener("DOMContentLoaded", function () {
        status = "show";
        showContent();
    });

    $('#editButton').on('click', function() {
        status = "form";
        showContent();
    });

    function showContent() {
        if (status === "form") {
            $('#form-content').show();
            $('#editButton').hide();
            $('#show-content').hide();
            $('#saveButton').show();
        } else {
            $('#form-content').hide();
            $('#editButton').show();
            $('#show-content').show();
            $('#saveButton').hide();
        }
    }
</script>

{{-- File Upload Script --}}
<script>
    let selectedFiles = [];
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-upload');
    const uploadText = document.getElementById('upload-text');
    const filePreview = document.getElementById('file-preview');


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
        const files = Array.from(e.dataTransfer.files);
        selectedFiles = selectedFiles.concat(files);
        handleFiles();
    });

    fileInput.addEventListener('change', function() {
        const files = Array.from(fileInput.files);
        selectedFiles = selectedFiles.concat(files);
        handleFiles();
    });

    function handleFiles() {
        if (selectedFiles.length > 0) {
            renderFilePreview();
        } else {
            filePreview.style.display = 'none';
            uploadText.style.display = 'block';
        }
    }

    function renderFilePreview() {
        filePreview.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="file-name">${file.name}<span class="file-size"> (${sizeMB} MB)</span></div>
                <span class="remove-file" data-index="${index}">&times;</span>
            `;
            filePreview.appendChild(fileItem);
        });

        filePreview.style.display = 'flex';
        uploadText.style.display = 'none';
    }

    filePreview.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-file')) {
            const index = e.target.getAttribute('data-index');
            selectedFiles.splice(index, 1);
            handleFiles();

            document.querySelectorAll('.file-error-message').forEach(el => el.remove());
            filePreview.classList.remove('is-invalid');
        }
    });
</script>

@endsection