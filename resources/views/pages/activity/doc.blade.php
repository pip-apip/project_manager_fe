@extends('layouts.app')

@section('title', 'Activity Page')

@section('content')

<style>
    /* Universal Modal Styling */
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

    /* Modal Content - Desktop */
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

    /* Image */
    #imageViewer {
        max-width: 100%;
        max-height: 75vh;
        border-radius: 8px;
        object-fit: contain;
    }

    /* Close Button */
    .closeImage {
        position: fixed;
        top: 20px;
        right: 20px;
        font-size: 28px;
        color: white;
        cursor: pointer;
        z-index: 10000; /* Ensure it's above all elements */
        transition: transform 0.2s;
    }

    .closeImage:hover {
        transform: scale(1.2);
    }

    /* 🔥 Responsive for Mobile */
    @media screen and (max-width: 768px) {
        .modern-modal-content {
            width: 95%;
            max-height: 70vh;
            padding: 10px;
        }

        #imageViewer {
            min-height: 50vh;
        }

        .closeImage {
            font-size: 24px;
            top: 10px;
            right: 10px;
        }
    }

    /* Fade-in Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

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

    .tags-form {
        margin-top: 80px;
    }

    @media screen and (max-width: 768px) {
        .tags-form {
            margin-top: 130px;
        }
    }
</style>


@php
    $activity = $data['activity'][0];
    $doc = $data['docActivity'];
    $categoryDoc = $data['categoryDoc'];
@endphp


@php
    $lastRoute = session()->get('lastRoute');
    $lastRoute = $lastRoute ? explode(',', $lastRoute) : [];
@endphp

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Detail <span class="d-none d-md-inline-block">Aktivitas</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ isset($lastRoute[0], $lastRoute[1]) ? route($lastRoute[0], $lastRoute[1]) : route('activity.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form form-vertical">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Nama Proyek</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $activity['project_name'] }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Tanggal</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($activity['start_date'])->translatedFormat('d-m-Y') }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Kategori Aktivitas</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $activity['activity_category_name'] }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Catatan Aktivitas</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $activity['title'] }}" readonly />
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
                            <h1 class="card-title">Dokumen Aktivitas</h1>
                        </div>
                        <div class="card-body" id="form_MOM" style="display: none">
                            <form action="" id="form" class="form form-vertical">
                                @csrf
                                <input type="text" name="activity_id" id="activity_id" hidden>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Judul Dokumen <code>*</code></label>
                                    </div>
                                    <div class="form-group col-md-10">
                                        <input type="text" placeholder="Masukkan Judul Dokumen Kegiatan" class="form-control" name="title" id="title_activity_doc">
                                    </div>

                                    <div class="col-md-2">
                                        <label>Deskripsi <code>*</code></label>
                                    </div>
                                    <div class="form-group col-md-10">
                                        <div id="quillEditor" style="height: 300px;"></div>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Tags <code>*</code></label>
                                    </div>
                                    <div class="form-group col-md-10">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="tagInput" placeholder="Ketik dan Tekan Enter..." name="tags"/>
                                        </div>
                                        <div class="tag-container" id="tags"></div>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Berkas Dokumentasi</label>
                                    </div>
                                    <div class="col-md-10">
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
                                    <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                        <button type="button"
                                            class="btn btn-primary me-1 mb-1" id="submitButton" onclick="storeDoc()">Simpan</button>
                                        <button type="reset"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body" id="show_MOM" style="display: none">
                            <label><b>Judul Dokumen :</b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="title_show"></p>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label><b>Deskripsi : </b></label>
                                    <div class="form-group">
                                        <div class="form-control-static" id="desc_show"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <label><b>Tags : </b></label>
                                    <div class="tag-container" id="tags_show">
                                    </div>
                                </div>
                                {{-- @if ($data['docActivity'] !== []) --}}
                                <div class="col-sm-12">
                                    <label><b>Dokumentasi :</b></label>
                                    <table style="width: 100%">
                                        <tr>
                                            @if($data['docActivity'] !== [])
                                            @php $url = "https://bepm.hanatekindo.com"; $i = 0; @endphp
                                            @foreach ($data['docActivity'][0]['files'] as $doc)
                                                <td style="text-align: center;">
                                                    <a onclick="openModernModal('{{ $url . $doc }}')" style="text-decoration: none; color: grey; font-size: 40px; cursor: pointer">
                                                        <i class="fa-solid fa-file-image"></i>
                                                    </a>
                                                </td>
                                            @endforeach
                                            @endif
                                        </tr>
                                        <tr>
                                            @if($data['docActivity'] !== [])
                                            @foreach ($data['docActivity'][0]['files'] as $doc)
                                                <td style="text-align: center;">Dokumentasi {{ $i+1 }}</td>
                                                @php $i++; @endphp
                                            @endforeach
                                            @endif
                                        </tr>
                                    </table>
                                </div>
                                {{-- @endif --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<div id="modernImageModal" class="modern-modal" style="display: none" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modern-modal-content">
        <img id="modernImagePreview" alt="Preview">
    </div>
    <span class="closeImage" onclick="closeModernModal()">&times;</span>
</div>

{{-- JQuery --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
{{-- SimpleDatatables - Template --}}
<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
{{-- Filepond - Template --}}
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
{{-- Quill.js --}}
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

{{-- @if(session()->has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session()->get('success') }}',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            $('#fullPageLoader').hide();
            location.reload();
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
@endif --}}

<script>
    const quill = new Quill('#quillEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'script': 'sub'
                }, {
                    'script': 'super'
                }],
                [{
                    'indent': '-1'
                }, {
                    'indent': '+1'
                }],
                [{
                    'direction': 'rtl'
                }],
                [{
                    'size': ['small', false, 'large', 'huge']
                }],
                [{
                    'header': [1, 2, 3, false]
                }],
                [{
                    'color': []
                }, {
                    'background': []
                }],
                [{
                    'font': []
                }],
                [{
                    'align': []
                }],
                ['clean']
            ]
        }
    });

    // console.log({!! json_encode(session()->get('lastRoute')) !!});
    let tags = [];

    function renderTags() {
        let tagContainer = $("#tags");
        tagContainer.find(".tag").remove();

        if (tags.length === 0) return;

        tags.forEach((tag, index) => {
            let tagElement = $(`
                <div class="tag">
                    ${tag}
                    <span class="remove" data-index="${index}">&times;</span>
                </div>
            `);
            tagContainer.append(tagElement);
        });
    }

    $(document).on("keydown", "#tagInput", function (event) {
        if (event.key === "Enter" && this.value.trim() !== "") {
            event.preventDefault();

            let tagText = this.value.trim().toLowerCase();

            if (!tags.includes(tagText)) {
                tags.push(tagText);
                renderTags();
            }

            this.value = "";
        }
    });

    $(document).on("click", ".remove", function () {
        let index = $(this).data("index");
        tags.splice(index, 1);
        renderTags();
    });
</script>

<script>
    let doc = {!! json_encode($data['docActivity']) !!}

    document.addEventListener("DOMContentLoaded", function () {
        let selectedValue = $('#documentCat2').val() ?? $('#documentCat1').val();
        showCardDoc(selectedValue);
    });

    $("#documentCat1, #documentCat2").change(async function () {
        $("#form")[0].reset();
        $('#form_MOM').hide();
        $('#show_MOM').hide();

        let selectedValue = $(this).val();
        showCardDoc(selectedValue);
    });

    function showCardDoc(selectedValue){
        if(selectedValue !== "#"){
            // console.log("Selected Value:", selectedValue);
            let filteredDocs = doc.filter(item => item.activity_doc_category_id == selectedValue);
            if(filteredDocs.length !== 0){
                // console.log("Filtered Documents:", filteredDocs);
                showDoc(filteredDocs);
            }else{
                // console.log("not document exist")
                $('#form_MOM').show();
                $('#activity_id').val("{{ $activity['id'] }}");
                $('#category_id').val(selectedValue);
            }
        }
    }

    function showDoc(data) {
        console.log(data);
        let title = data[0]['title']
        let description = data[0]['description']
        let tags = data[0]['tags'];
        let id = data[0]['id'];

        $('#title_show').text(title);
        $('#desc_show').html(description);

        let tagContainer = $("#tags_show");
        tagContainer.find(".tag").remove();

        tags.forEach((tag) => {
            let tagElement = $(`
                <div class="tag">
                    ${tag}
                </div>
            `);
            tagContainer.append(tagElement);
        });
        $('#show_MOM').show();
        $('#btnDelete').attr('onclick', `confirmDelete('${"{{ route('activity.doc.delete', ':id') }}".replace(':id', id)}')`);
    }

    function storeDoc() {
        buttonLoadingStart("submitButton");
        let formData = new FormData(document.getElementById("form"));
        formData.append('description', quill.root.innerHTML);
        formData.append('tags', JSON.stringify(tags));
        selectedFiles.forEach((file, index) => {
            formData.append('files[]', file);  // proper file upload
        });
        $.ajax({
            url: "{{ route('activity.doc.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response);
                buttonLoadingEnd("submitButton");
                if(response.status === 400 || response.status === 500){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        const errors = response.errors || {};

                        // Reset previous error highlights
                        $('#file-preview').removeClass('is-invalid');
                        $('.file-item .file-error').remove();

                        let fileErrorFound = false;

                        // Handle file-specific errors (e.g. files.0, files.1)
                        Object.keys(errors).forEach(function (key) {
                            if (key.startsWith('files.')) {
                                const index = parseInt(key.split('.')[1]);
                                const message = errors[key][0];

                                const fileItems = document.querySelectorAll('.file-item');
                                if (fileItems[index]) {
                                    fileErrorFound = true;
                                    const errorEl = document.createElement('div');
                                    errorEl.className = 'file-error';
                                    errorEl.style.color = '#e74c3c';
                                    errorEl.style.fontSize = '12px';
                                    errorEl.textContent = message;
                                    fileItems[index].insertAdjacentElement('afterend', errorEl);
                                }
                            } else {
                                // Other form errors
                                let inputField = $(`[name="${key}"]`);
                                inputField.addClass("is-invalid")
                                    .after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                            }
                        });

                        if (fileErrorFound) {
                            $('#file-preview').addClass('is-invalid');
                        }
                    });

                }else{
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function (xhr) {
                console.error('Error:', xhr);
            }
        });
        // console.log(selectedFiles);
    }

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
            console.log(result)
            if (result.isConfirmed) {
                $('#fullPageLoader').show();
                window.location.href = url;
            }
        });
    }

    function openModernModal(imageSrc) {
        document.getElementById('modernImagePreview').src = imageSrc;
        document.getElementById('modernImageModal').style.display = "flex";
    }

    function closeModernModal() {
        document.getElementById('modernImageModal').style.display = "none";
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