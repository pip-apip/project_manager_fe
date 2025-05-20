@extends('layouts.app')

@section('title', 'Project Page')

@section('content')

<style>
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

    .remove-file,
    .remove-edited-file,
    .remove-new-file {
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

    .remove-file,
    .remove-new-file,
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
                        <form action="{{ route('progress.store', $project['id']) }}" method="POST" enctype="multipart/form-data">
                            @csrf
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
                                        @if (count($activityCategory) == 0)
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @else
                                        @foreach ($activityCategory as $cat)
                                            <tr>
                                                <td>{{ $cat['name'] }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center">
                                                        <input type="text" class="form-control text-center" id="progress_{{ $cat['id'] }}" name="progress_{{ $cat['id'] }}" value="{{ $cat['value'] ?? '0' }}" autocomplete="off">
                                                        <span class="ms-2">%</span>
                                                    </div>
                                                </td>
                                                <td width="15%">
                                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" name="note_{{ $cat['id'] }}" placeholder="Masukkan Keterangan">{{ $cat['note'] ?? '' }}</textarea>
                                                </td>
                                                <td width="5%" class="text-center">
                                                    <button type="button" onclick="showDetail({{ $cat['id'] }})" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#detailModal"><i class="fa-solid fa-image"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12 d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary me-1 mb-1" id="submitButtonPage1">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade text-left w-100" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Progress Image</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="closeDetailModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div>
                <div class="modal-body">
                    <input type="text" id="activity_id" style="display: none">
                    <div class="row">
                        <div class="col-sm-12" id="show-content">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th class="text-center">Preview</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="images-list">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12" id="form-content" style="display: none">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label>Antrian Upload Gambar</label>
                            </div>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger ml-1" id="save-button" style="display: none" onclick="saveImage()">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-success ml-1" id="addButton">
                        <i class="fa-solid fa-plus"></i> Tambah Dokumen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    let idActivityOpened = null;
    let images = [];
    let selectedFiles = [];
    const updateFiles = [];
    const replacePaths = [];
    const deletePaths = [];
    const activities = @json($activityCategory);
    let activity = null;

    function showDetail(id) {
        idActivityOpened = id;
        $('#activity_id').val(id);
        activity = activities.find(activity => activity.id === id);

        let path = "https://bepm.hanatekindo.com/storage/"
        images = activity.images || [];

        let html = '';
        if (activity.images.length > 0) {
            activity.images.forEach((image, key) => {
                let fileName = image.split('/').pop();
                html += `
                    <tr>
                        <td>${fileName}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm" onclick="window.open('${path}${image}', '_blank')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-warning btn-sm" onclick="editImage(${key}, '${image}')">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteImage(${key}, '${image}')">
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
        $('#images-list').html(html);
    }

    function editImage(index, path) {
        console.log('Edit image at index:', index);
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';

        input.onchange = function () {
            const newFile = input.files[0];
            if (newFile) {
                updateFiles[index] = newFile;
                replacePaths[index] = path;
                handleFiles();

                const row = $(`#images-list tr:eq(${index})`);
                if (!row.hasClass('deleted')) {
                    row.addClass('edited');
                }

                row.find('button').prop('disabled', true);
            }
        };

        input.click();
    }

    function deleteImage(index, path) {
        console.log('Delete image at index:', index);

        // Tambahkan ke list file yang akan dihapus
        deletePaths.push(path);

        // Jika file ini sebelumnya diedit, batalkan edit-nya
        delete updateFiles[index];
        delete replacePaths[index];

        const row = $(`#images-list tr:eq(${index})`);
        row.addClass('deleted');
        row.find('button').prop('disabled', true);

        handleFiles();
    }

    function updateImage(id){
        buttonLoadingStart("save-button");
        const formData = new FormData();

        // New files
        selectedFiles.forEach(file => {
            formData.append('new_files[]', file);
        });

        // Edited files + path yang diganti
        updateFiles.forEach((file, index) => {
            if (file) {
                formData.append('update_files[]', file);
                formData.append('replace_paths[]', replacePaths[index] || '');
                formData.append('update_indexes[]', index); // Kirim index juga
            }
        });

        // Deleted files
        deletePaths.forEach(path => {
            formData.append('remove_images[]', path);
        });

        formData.append('_token', '{{ csrf_token() }}');

        // Debug
        console.log({
            new_files: selectedFiles,
            update: updateFiles,
            delete: deletePaths
        });

        $.ajax({
            url: `{{ route('progress.updateImage', ':id') }}`.replace(':id', idActivityOpened),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Upload successful:', response);
                buttonLoadingEnd("save-button");
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Images uploaded successfully.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr, status, error) {
                console.error('Upload failed:', error);
                alert('Failed to upload files. Please try again.');
            }
        });
    }

    function saveImage() {
        let formData = new FormData();
        selectedFiles.forEach((file) => {
            formData.append('files[]', file);
        });
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: `{{ route('progress.storeImage', ':id') }}`.replace(':id', idActivityOpened),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Upload successful:', response);
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Images uploaded successfully.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr, status, error) {
                console.error('Upload failed:', error);
                alert('Failed to upload files. Please try again.');
            }
        });
    }
</script>

{{-- File Upload Script --}}
<script>
    const fileInput = $('#file-upload');
    const uploadText = $('#upload-text');
    const filePreview = $('#file-preview');
    const dropzone = $('#dropzone');
    const addButton = $('#addButton');

    dropzone.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    dropzone.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });

    dropzone.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        const files = Array.from(e.originalEvent.dataTransfer.files);
        selectedFiles = selectedFiles.concat(files);
        handleFiles();
    });

    fileInput.on('change', function() {
        const files = Array.from(this.files);
        selectedFiles = selectedFiles.concat(files);
        handleFiles();
    });

    addButton.on('click', function() {
        fileInput.click();
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
                $('#form-content').show();
            } else {
                $('#form-content').hide();
            }

            const hasExistingImages = activity && activity.images && activity.images.length > 0;

            if (hasExistingImages || hasDeletedFiles) {
                $('#save-button').attr('onclick', 'updateImage()').show();
            } else {
                $('#save-button').attr('onclick', 'saveImage()').show();
            }
        } else {
            $('#form-content').hide();
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
