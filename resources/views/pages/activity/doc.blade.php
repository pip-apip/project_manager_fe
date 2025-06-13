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
    tr.deleted td {
        text-decoration: line-through;
        color: #a0a0a0;
        background-color: #f9d6d5;
    }

    tr.edited td {
        text-decoration: line-through;
        color: #a0a0a0;
        background-color: #f9d6d5;
    }
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

    .remove-new-file,
    .remove-edited-file {
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

    .remove-new-file:hover,
    .remove-edited-file:hover {
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h1 class="card-title">Dokumen Aktivitas</h1>
                            @if($data['docActivity'])
                            <div>
                                <button class="btn btn-warning btn-sm me-2" id="actionButton" onclick="editDoc()"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-danger btn-sm" id="deleteButton" onclick="confirmDelete('{{ route('activity.doc.delete', ':id') }}')"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            @endif
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
                                        <input type="text" placeholder="Masukkan Judul Dokumen Kegiatan" class="form-control" name="title" id="title_activity_doc" autocomplete="off" />
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
                                    <div class="col-md-10 row" id="table-document" style="display: none">

                                    </div>
                                    {{-- <div class="col-md-10 offset-md-2" id="dropzone-container"> --}}
                                    <div class="col-md-10" id="dropzone-container" style="display: none">
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
                                    <label class="mb-2"><b>Dokumentasi :</b></label>
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%" class="text-center">No</th>
                                                    <th style="width: 60%" >Nama File</th>
                                                    <th style="width: 11%" class="text-center">Extension</th>
                                                    <th style="width: 15%" class="text-center">Ukuran</th>
                                                    <th style="width: 9%" class="text-center">Preview</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                                @php $i = 1; $url = env('API_BASE_URL_MAIN').'//storage/';@endphp
                                                @if ($data['docActivity'])
                                                @foreach ($data['docActivity'][0]['files'] as $doc)
                                                    <tr>
                                                        <td class="text-center">{{ $i }}</td>
                                                        <td>{{ pathinfo($doc['url'], PATHINFO_FILENAME) }}</td>
                                                        <td class="text-center">{{ pathinfo($doc['url'], PATHINFO_EXTENSION) }}</td>
                                                        <td class="text-center">
                                                            {{ $doc['size'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if (pathinfo($doc['url'], PATHINFO_EXTENSION) == 'pdf')
                                                                <a href="{{ $url . $doc['url'] }}" target="_blank" style="text-decoration: none; color: grey; font-size: 20px;">
                                                                    <i class="fa-solid fa-file-pdf"></i>
                                                                </a>
                                                            @else
                                                                <a onclick="openModernModal('{{ $url . $doc['url'] }}')" style="text-decoration: none; color: grey; font-size: 20px; cursor: pointer">
                                                                    <i class="fa-solid fa-file-image"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php $i++; @endphp
                                                @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- <table style="width: 100%">
                                        <tr>
                                            @if($data['docActivity'] !== [])
                                            @php $url = env('API_BASE_URL_MAIN').`/"; $i = 0; @endphp
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
                                    </table> --}}
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
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean']
            ]
        }
    });

    let tags = [];

    function renderTags() {
        const tagContainer = $("#tags").empty();
        tags.forEach((tag, i) => {
            tagContainer.append(`
                <div class="tag">${tag}
                    <span class="remove" data-index="${i}">&times;</span>
                </div>`);
        });
    }

    $(document).on("keydown", "#tagInput", function (e) {
        if (e.key === "Enter" && this.value.trim() !== "") {
            e.preventDefault();
            const tagText = this.value.trim().toLowerCase();
            if (!tags.includes(tagText)) tags.push(tagText);
            renderTags();
            this.value = "";
        }
    });

    $(document).on("click", ".remove", function () {
        tags.splice($(this).data("index"), 1);
        renderTags();
    });
</script>

<script>
    let doc = {!! json_encode($data['docActivity']) !!}

    document.addEventListener("DOMContentLoaded", function () {
        let selectedValue = $('#documentCat2').val() ?? $('#documentCat1').val();
        showCardDoc(selectedValue);
        if(doc){
            $('#dropzone-container').show();
        }else{
            $('#dropzone-container').hide();
        }
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
        // console.log(data);
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

    function editDoc() {
        $('#actionButton').attr('onclick', 'cancelEdit()').attr('class', 'btn btn-secondary btn-sm').html('<i class="fa-solid fa-xmark"></i>');
        let docData = doc[0];
        if (!docData) return;

        $('#show_MOM').hide();
        $('#form_MOM').show();

        $('#activity_id').val(docData.activity_id);
        $('#title_activity_doc').val(docData.title);
        $('#category_id').val(docData.activity_doc_category_id);

        quill.clipboard.dangerouslyPasteHTML(docData.description);

        tags = docData.tags || [];
        renderTags();

        let path = "{{ env('API_BASE_URL_MAIN') }}/storage/";
        let html = '';
        let html_file_list = '';
        if (docData.files.length > 0) {
            html +=`
                <div class="col-md-11">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th class="text-center">Preview</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="file-list">

                    </tbody>
                </table>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-primary btn-sm" id="addButton"><i class="fa-solid fa-plus"></i></button>
                </div>
            `;
            docData.files.forEach((file, key) => {
                // console.log(file)
                let fileName = file.url.split('/').pop();
                fileNameSplit = fileName.split('-');
                fileName = fileNameSplit[2];
                if(fileNameSplit.length > 2){
                    for(let i = 3; i < fileNameSplit.length; i++){
                        fileName += " |" + fileNameSplit[i];
                    }
                }
                html_file_list += `
                    <tr>
                        <td>${fileName}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm" onclick="window.open('${path}${file.url}', '_blank')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-warning btn-sm" onclick="editFile(${key}, '${file.url}')">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteFile(${key}, '${file.url}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            html = `
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data</td>
                </tr>
            `;
        }
        $('#table-document').html(html).show();
        $('#file-list').html(html_file_list);
        $('#dropzone-container').attr('class', 'offset-md-2 col-md-10').hide();

        $('#submitButton').text('Perbarui').attr('onclick', 'updateDoc()');
    }

    function editFile(index, path) {
        // console.log('Edit file at index:', index);
        const input = document.createElement('input');
        input.type = 'file';

        input.onchange = function () {
            const newFile = input.files[0];
            if (newFile) {
                updateFiles[index] = newFile;
                replacePaths[index] = path;
                handleFiles();

                const row = $(`#file-list tr:eq(${index})`);
                if (!row.hasClass('deleted')) {
                    row.addClass('edited');
                }

                row.find('button').prop('disabled', true);
            }
        };

        input.click();
    }

    function deleteFile(index, path) {
        // console.log('Delete file at index:', index);

        // Tambahkan ke list file yang akan dihapus
        deletePaths.push(path);

        // Jika file ini sebelumnya diedit, batalkan edit-nya
        delete updateFiles[index];
        delete replacePaths[index];

        const row = $(`#file-list tr:eq(${index})`);
        row.addClass('deleted');
        row.find('button').prop('disabled', true);

        handleFiles();
    }

    function cancelEdit() {
        $('#actionButton').attr('onclick', 'editDoc()').attr('class', 'btn btn-warning btn-sm').html('<i class="fa-solid fa-pen"></i>');
        $('#show_MOM').show();
        $('#form_MOM').hide();
    }

    function updateDoc() {
        buttonLoadingStart("submitButton");
        const formData = new FormData(document.getElementById("form"));
        formData.append('description', quill.root.innerHTML);
        formData.append('tags', JSON.stringify(tags));

        // New files
        selectedFiles.forEach(file => {
            formData.append('new_files[]', file);
        });

        // Edited files + path yang diganti
        updateFiles.forEach((file, index) => {
            if (file) {
                formData.append('update_files[]', file);
                formData.append('replace_paths[]', replacePaths[index] || '');
                formData.append('update_indexes[]', index);
            }
        });

        // Deleted files
        deletePaths.forEach(path => {
            formData.append('remove_files[]', path);
        });

        formData.append('_token', '{{ csrf_token() }}');

        // Debug
        // console.log({
        //     new_files: selectedFiles,
        //     update: updateFiles,
        //     delete: deletePaths
        // });

        $.ajax({
            url: `{{ route('activity.doc.update', ':id') }}`.replace(':id', doc[0].id),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                // console.log('response',response);
                buttonLoadingEnd("submitButton");
                if(response.status === 400 || response.status === 500){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
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
            error: function (xhr, status, error) {
                buttonLoadingEnd("submitButton");
                console.error('Upload failed:', error);
                alert('Failed to upload files. Please try again.');
            }
        });

        // [...formData.entries()].forEach(([key, value]) => {
        //     console.log(key, value);
        // });
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
                // console.log('response',response);
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
            // console.log(result)
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
    const updateFiles = [];
    const replacePaths = [];
    const deletePaths = [];
    const dropzone = $('#dropzone');
    const fileInput = $('#file-upload');
    const uploadText = $('#upload-text');
    const filePreview = $('#file-preview');
    const addButton = $('#addButton');

    dropzone.on('dragover', function(e) {
        e.preventDefault();
        dropzone.addClass('dragover');
    });

    dropzone.on('dragleave', function(e) {
        e.preventDefault();
        dropzone.removeClass('dragover');
    });

    dropzone.on('drop', function(e) {
        e.preventDefault();
        dropzone.removeClass('dragover');
        const files = Array.from(e.originalEvent.dataTransfer.files);
        selectedFiles = selectedFiles.concat(files);
        handleFiles();
    });

    fileInput.on('change', function() {
        const files = Array.from(fileInput[0].files);
        selectedFiles = selectedFiles.concat(files);
        handleFiles();
    });

    $(document).on('click', '#addButton', function () {
        $('#file-upload').click();
    });

    function handleFiles() {
        const hasNewFiles = selectedFiles.length > 0;
        const hasEditedFiles = updateFiles.some(file => file !== undefined);
        const hasDeletedFiles = deletePaths.length > 0;

        // Tampilkan file preview hanya jika ada file baru atau yang diedit
        if (hasNewFiles || hasEditedFiles) {
            renderFilePreview();      // Fungsi untuk menampilkan preview file
            filePreview.show();
            uploadText.hide();
        } else {
            filePreview.hide();
            uploadText.show();
        }

        // Tampilkan form jika ada perubahan apa pun (new/edit/delete)
        if (hasNewFiles || hasEditedFiles || hasDeletedFiles) {
            if (hasNewFiles || hasEditedFiles) {
                $('#dropzone-container').show();
            } else {
                $('#dropzone-container').hide();
            }
        } else {
            $('#dropzone-container').hide();
            $('#save-button').hide();
        }
    }

    function renderFilePreview() {
        filePreview.empty();

        // 1. Tampilkan file hasil edit (updateFiles)
        updateFiles.forEach((file, index) => {
            if (file) {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                const oldPath = replacePaths[index] || 'Unknown path';
                const fileItem = $(`
                    <div class="file-item new">
                        <div class="file-name">${file.name} <span class="file-size">(${sizeMB} MB, edited)</span><br>
                            <small>Replacing: ${oldPath}</small>
                        </div>
                        <span class="remove-edited-file" data-index="${index}">&times;</span>
                    </div>
                `);
                filePreview.append(fileItem);
            }
        });

        // 2. Tampilkan file baru (selectedFiles)
        selectedFiles.forEach((file, index) => {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const fileItem = $(`
                <div class="file-item new">
                    <div class="file-name">${file.name} <span class="file-size">(${sizeMB} MB)</span></div>
                    <span class="remove-new-file" data-index="${index}">&times;</span>
                </div>
            `);
            filePreview.append(fileItem);
        });

        // 3. Tampilkan preview hanya jika ada file baru atau edit
        const hasPreview = selectedFiles.length > 0 || updateFiles.some(f => f !== undefined);
        if (hasPreview) {
            filePreview.css('display', 'flex');
            uploadText.hide();
        } else {
            filePreview.hide();
            uploadText.show();
        }
    }

    filePreview.on('click', '.remove-edited-file', function () {
        const index = $(this).data('index');
        delete updateFiles[index];
        delete replacePaths[index];
        handleFiles();
    });

    filePreview.on('click', '.remove-new-file', function () {
        const index = $(this).data('index');
        selectedFiles.splice(index, 1);
        handleFiles();
    });
</script>


@endsection