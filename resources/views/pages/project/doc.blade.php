@extends('layouts.app')

@section('title', 'Project Page')

@section('content')

<style>
    /* Modern Modal Styles */
    .modern-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.3s ease-in-out;
        padding: 15px;
    }

    .modern-modal-content {
        position: relative;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 15px;
        width: 90%;
        max-width: 800px;
        max-height: 85vh;
        text-align: center;
        animation: fadeIn 0.3s ease-in-out;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
    }

    /* PDF Viewer */
    #pdfViewer {
        width: 100%;
        height: 75vh;
        border-radius: 8px;
    }

    /* Close Button */
    .closePDF {
        position: fixed;
        top: 20px;
        right: 20px;
        font-size: 28px;
        color: white;
        cursor: pointer;
        z-index: 10000; /* Ensure it's above all elements */
        transition: transform 0.2s;
    }

    .closePDF:hover {
        transform: scale(1.2);
    }

    /* File Upload Wrapper */
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
        align-items: center;
        justify-content: space-between;
        background-color: #5f5b59;
        padding: 8px 12px;
        border-radius: 10px;
        color: white;
        width: 100%;
        margin-top: 10px;
        font-size: 14px;
    }

    .file-info {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 90%;
    }

    .remove-file {
        background-color: #333;
        color: white;
        border-radius: 50%;
        padding: 2px 8px;
        margin-left: 8px;
        font-weight: bold;
        cursor: pointer;
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


    /* 🔥 Responsive for Mobile */
    @media screen and (max-width: 768px) {
        .modern-modal-content {
            width: 95%;
            max-height: 70vh;
            padding: 10px;
        }

        #pdfViewer{
            min-height: 50vh;
        }

        .closePDF {
            font-size: 24px;
            top: 5px;
            right: 10px;
        }
    }

    /* Fade-in Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

@php
    $project = $data['project'];
    $doc = $data['docProject'];
    $categoryDoc = $data['categoryDoc'];
@endphp

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Dokumen Administrasi <span class="d-none d-md-inline-block">Proyek</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('project.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('project.store.doc') }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="project_id" id="project_id" value="{{ $project['id'] }}" />
                        <div class="row">
                            <div class="col-md-2">
                                <label>Nama Proyek</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="project_name_doc" value="{{ $project['name'] }}" readonly />
                            </div>

                            <div class="col-md-2">
                                <label>Kategori Dokumen <code>*</code></label>
                            </div>
                            <fieldset class="form-group col-md-10">
                                <select class="form-select @error('admin_doc_category_id') is-invalid @enderror" id="category_input" name="admin_doc_category_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categoryDoc as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('admin_doc_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </fieldset>

                            <div class="col-md-2">
                                <label>Catatan <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Catatan Dokumen Administrasi" class="form-control @error('title') is-invalid @enderror" name="title" id="title" autocomplete="off" />
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                            </div>

                            <div class="col-md-2">
                                <label>Berkas Dokumen</label>
                            </div>
                            <div class="col-md-10">
                                <div class="file-upload-wrapper" id="dropzone">
                                    <label for="file-upload" class="file-upload-area @error('file') is-invalid @enderror">
                                        <div class="upload-text" id="upload-text">
                                            Drag & Drop your files or <span class="browse">Browse</span>
                                        </div>
                                        <input type="file" id="file-upload" name="file" />
                                        <div class="file-preview" id="file-preview" style="display: none;">
                                            <span class="file-info" id="file-name"></span>
                                            <span class="remove-file" id="remove-file">&times;</span>
                                        </div>
                                    </label>
                                </div>
                                @error('file')
                                <small class="file-error-text" style="color: #e74c3c;" id="file-error">
                                    {{ $message }}
                                </small>
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

<div class="page-heading">
    <div class="page-content">
        <section class="section">
            <div class="row">
                <div class="col-sm-12" id="tagsSearch">
                    <div class="card">
                        <div class="card-header">
                            <h1 class="card-title">Daftar Dokumen</h1>
                        </div>
                        <div class="card-body">
                            <fieldset class="form-group">
                                <select class="form-select" id="category_show">
                                    <option value="#">Pilih Kategori Dokumen</option>
                                    @foreach ($categoryDoc as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                            <hr />
                            <div class="table-responsive">
                                <table class="table table-striped" id="table">
                                    <thead>
                                        <tr>
                                            <th width="25%">Kategori</th>
                                            <th>Catatan</th>
                                            <th width="5%" class="text-center">Berkas</th>
                                            <th width="5%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div id="modernPDFModal" class="modern-modal" style="display: none">
    <div class="modern-modal-content">
        <span class="closePDF" onclick="closePDFModal()">&times;</span>
        <iframe id="pdfViewer" frameborder="0"></iframe>
    </div>
</div>

<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
<!-- filepond -->
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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

    let data_doc = {!! json_encode($doc) !!};

    $(document).ready(function () {
        console.log(data_doc);
        showDataDoc(data_doc);
    });

    function showDataDoc(data) {
        $('#table_body').empty();
        let url = "https://bepm.hanatekindo.com";
        let rows = "";
        if (data.length == 0) {
            rows += `
                <tr>
                    <td colspan="4" class="text-center">Tidak Ada Dokumen</td>
                </tr>`;
        }else{
            $.each(data, function (index, doc) {
                let deleteUrl = `{{ route('project.destroy.doc', ':id') }}`.replace(':id', doc['id']);
                rows += `
                    <tr>`;
                    rows += `<td>${doc['admin_doc_category_name']}</td>
                        <td>${doc['title']}</td>
                        <td class="text-center"><a onclick="openPDFModal('${url}${ doc.file }')" style="text-decoration: none; color: grey"><i class="fa-solid fa-file-pdf"></i></a></td>
                        <td class="text-center">
                            <a href="javascript:void(0)" class="btn btn-danger ml-1 btn-sm" onclick="confirmDelete('${deleteUrl}')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>`;
            });
        }
        $("#table_body").html(rows);
    }

    // $('#category_show').change(function(){
    //     console.log(data_doc);
    //     let id_category = $(this).val();
    //     let url = "https://bepm.hanatekindo.com";

    //     let filteredDoc = data_doc.filter(doc =>
    //         doc.admin_doc_category.id == id_category
    //     );
    //     console.log("Filtered Doc:", filteredDoc);
    //     $('#data_doc').empty();
    //     $('#data_doc').html(`<hr>
    //         <div class="col-sm-3"></div>
    //         <div class="col-sm-6">
    //             <table id="table_doc">
    //             </table>
    //         </div>
    //         <div class="col-sm-3"></div>
    //             `);
    //     $("#table_doc").empty();
    //     if(filteredDoc.length > 0){
    //         let rows2 = "";
    //         $.each(filteredDoc, function (index, doc) {
    //             rows2 += `
    //             <tr class="mt-2">
    //                 <td><a onclick="openPDFModal('${url}${ doc.file }')" style="text-decoration: none; color: grey"><i class="fa-solid fa-file-pdf"></i></a></td>
    //                 <td width="400px">${doc.title}</td>
    //                 <td>
    //                     <a href="/project/destroyDoc/${doc.id}" class="btn btn-danger ml-1 btn-sm" onclick="return confirmDelete(this, '{{ csrf_token() }}')">
    //                         <i class="bx bx-check d-block d-sm-none"></i>
    //                         <span class="d-none d-sm-block"><i class="fa-solid fa-trash"></i></span>
    //                     </a>
    //                 </td>
    //             </tr>`;
    //         });

    //         $("#table_doc").html(rows2);
    //     }else{
    //         let rows2 = "";
    //             rows2 += `
    //             <tr class="mt-2" style="text-align: center;">
    //                 <td style="padding: 0 auto"><b>no files have been uploaded yet</b></td>
    //             </tr>`;

    //         $("#table_doc").html(rows2);
    //     }
    // });

    $('#category_show').change(function(){
        let id_category = $(this).val();
        if (id_category == "#") {
            showDataDoc(data_doc);
            return;
        }
        let filteredDoc = data_doc.filter(doc =>
            doc.admin_doc_category_id == id_category
        );
        showDataDoc(filteredDoc);
    });

    function confirmDelete(url) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Anda tidak dapat mengembalikan data yang dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#fullPageLoader').show();
                window.location.href = url;
            }
        });
    }
</script>

{{-- Modern Modal --}}
<script>
    function openPDFModal(pdfUrl) {
        document.getElementById('pdfViewer').src = pdfUrl;
        document.getElementById('modernPDFModal').style.display = "flex";
    }

    function closePDFModal() {
        document.getElementById('modernPDFModal').style.display = "none";
        document.getElementById('pdfViewer').src = "";
    }
</script>

{{-- File Upload Script --}}
<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-upload');
    const fileName = document.getElementById('file-name');
    const uploadText = document.getElementById('upload-text');
    const filePreview = document.getElementById('file-preview');
    const removeFileBtn = document.getElementById('remove-file');

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
        fileInput.files = files;
        handleFile(files);
    });

    fileInput.addEventListener('change', function() {
        handleFile(fileInput.files);
    });

    removeFileBtn.addEventListener('click', function () {
        fileInput.value = '';
        fileName.textContent = '';
        filePreview.style.display = 'none';
        uploadText.style.display = 'block';
    });

    function handleFile(files) {
        if (files.length > 0) {
            const file = files[0];
            const sizeMB = (file.size / (1024 * 1024)).toFixed(1);
            fileName.textContent = `${file.name} (${sizeMB} MB)`;
            filePreview.style.display = 'flex';
            uploadText.style.display = 'none';
        }
    }
</script>


</script>

@endsection
