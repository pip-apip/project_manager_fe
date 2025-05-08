@extends('layouts.app')

@section('title', 'Project Page')

@section('content')

<style>
    .scrollable-table {
        width: 100%;
        border-collapse: collapse;
    }

    .scrollable-table thead,
    .scrollable-table tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed; /* Prevents layout issues */
    }

    .scrollable-table tbody {
        display: block;
        max-height: 200px;
        overflow-y: auto;
    }

    .scrollable-table thead th input[type="text"] {
        width: 80%;
        padding: 2px 8px;
        font-size: 0.9rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        color: rgb(97, 112, 126);
        margin-left: 4px;
    }

    .scrollable-table thead th input[type="text"]:focus {
        outline: none;
        box-shadow: none;
        border-color: #ccc; /* atau warna border default yang kamu mau */
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>

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
                            <h1>{{ $status === 'create' ? 'Tambah' : 'Ubah' }} <span class="d-none d-md-inline-block">Aktivitas</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ isset($lastRoute[0], $lastRoute[1]) ? route($lastRoute[0], $lastRoute[1]) : route('activity.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($status === 'create')
                        <form action="{{ route('activity.store') }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @elseif ($status === 'edit')
                        <form action="{{ route('activity.update', $activity['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @endif
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <label>Nama Proyek <code>*</code></label>
                            </div>
                            <fieldset class="form-group col-md-10">
                                @if ($countDocAct > 0)
                                    <input type="text" name="project_id" id="project_id" value="{{ $activity['project_id'] }}" hidden />
                                @endif
                                <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id" {{ $countDocAct > 0 ? 'disabled' : '' }}>
                                    <option value="">Pilih Proyek</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project['id'] }}" {{ old('project_id', $projectId ?? $activity['project_id'] ?? '') == $project['id'] ? 'selected' : '' }}>
                                            {{ $project['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </fieldset>
                            {{-- @if ($countDocAct > 0)
                                <input type="text" name="project_id" id="project_id" value="{{ $activity['project']['id'] }}" hidden />
                            @endif --}}
                            <div class="col-md-2">
                                <label>Kategori Aktivitas <code>*</code></label>
                            </div>
                            <fieldset class="form-group col-md-10">
                                <select class="form-select" id="activity_category_id" name="activity_category_id">
                                    <option value="#">Pilih Kategori</option>
                                    @foreach ($categoryAct as $cat)
                                    <option value="{{ $cat['id'] }}" {{ old('activity_category_id', $activity ? $activity['activity_category_id'] : '') == $cat['id'] ? 'selected' : '' }}>
                                        {{ $cat['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </fieldset>
                            <div class="col-md-2">
                                <label>Catatan Aktivitas <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Catatan Aktivitas" class="form-control @error('title') is-invalid @enderror" name="title" id="title" value="{{ old('title', $activity ? $activity['title'] : '') }}"  autocomplete="off" />
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Tanggal Mulai <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" id="start_date" value="{{ old('start_date', $activity ? $activity['start_date'] : '') }}" autocomplete="off" />
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label>Member Aktivitas <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#teamModal" onclick="teamModal()">Tambah Member</button> <span id="countMember"> Total : 0 Member  </span>
                                <input type="text" name="activityTeam" id="activityTeam" style="display: none">
                            </div>

                            {{-- <div class="col-md-2">
                                <label>Tanggal Selesai <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date" id="end_date" value="{{ old('end_date', $activity ? $activity['end_date'] : '') }}" autocomplete="off" />
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}
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

<div class="modal fade text-left w-100" id="teamModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Tim Aktivitas</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="closeTeamModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div>
                <div class="modal-body">
                    <div class="row" id="teamInput" style="display: none">
                        <div class="col-sm-6">
                            <p class="text-center"><b>ALL USER</b></p>
                            <hr>
                            <table class="table table-striped mb-0 scrollable-table">
                                <thead>
                                    <tr>
                                        <th width="80%">Nama <input type="text" id="userSearch" autocomplete="off" /></th>
                                        <th width="20%" style="text-align: center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table_set">
                                </tbody>
                            </table>
                        </div>
                        <hr class="d-sm-none">
                        <div class="col-sm-6">
                            <p class="text-center"><b>FIX TEAM</b></p>
                            <hr>
                            <table class="table table-striped mb-0 scrollable-table">
                                <thead>
                                    <tr>
                                        <th width="80%">Nama <input type="text" id="teamSearch" autocomplete="off" /></th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table_fix">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="teamShow" style="display: none">
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-8">
                                <table class="table table-striped mb-0 scrollable-table">
                                    <thead>
                                        <tr>
                                            <th width="10%">No</th>
                                            <th width="90%">Nama</th>
                                            {{-- <th width="20%">Aksi</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody id="table_show">
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" id="footerTeam">
                    <button type="button" class="btn btn-success ml-1" onclick="exit()" id="submitButton">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="fullPageLoader" class="full-page-loader" style="display: none">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@if(session()->has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session()->get('success') }}',
        });
    </script>
@endif

@if(session()->has('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session()->get('error') }}',
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
    
    let access_token = @json(session('user.access_token'));

    document.addEventListener("DOMContentLoaded", function () {
        console.log(JSON.stringify(@json(session('lastRoute')), null, 2));
    });

    $(document).ready(function() {
        let projectSelected = $('#project_id').val();
        if(projectSelected == '') {
            $('#activity_category_id').prop('disabled', true);
        } else {
            showCategoryList(projectSelected);
            $('#activity_category_id').prop('disabled', false);
        }
        setUser();
    });

    $('#project_id').change(function() {
        let projectSelected = $(this).val();
        if(projectSelected == '') {
            $('#activity_category_id').prop('disabled', true);
        } else {
            showCategoryList(projectSelected);
            $('#activity_category_id').prop('disabled', false);
        }
    });

    function showCategoryList(project_id) {
        $.ajax({
            url: `https://bepm.hanatekindo.com/api/v1/activity-categories/search?project_id=${project_id}, 0`,
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            type: "GET",
            // data: {
            //     project_id: project_id
            // },
            success: function(response) {
                console.log(response);
                $('#activity_category_id').empty();
                $('#activity_category_id').append('<option value="">Pilih Kategori</option>');
                $.each(response.data, function(index, category) {
                    $('#activity_category_id').append('<option value="' + category.id + '">' + category.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });

    }
</script>

<script>
    let users = {!! isset($users) ? json_encode($users) : '[]' !!};
    // let teams = {!! isset($groupedTeams) ? json_encode($groupedTeams) : '[]' !!};
    let userSet = [];
    let teamFix = [];

    function exit() {
        $('#teamModal').modal('hide');
        $('#closeTeamModal').removeAttr('disabled');
        $('#countMember').text('Total : ' + teamFix.length + ' Member');
        $('#activityTeam').val(JSON.stringify(teamFix));
    }

    function teamModal(){
        renderTeam();
        renderUser();
        $('#teamShow').hide();
        $('#teamInput').show();
    }

    function setUser(){
        $.each(users, function (index, user) {
            userSet.push({
                name: user.name,
                id: user.id
            });
        });
    }

    function renderUser(){
        let rows = "";
        // let button = "";
        $('#table_set').empty();
        if(userSet.length > 0){
            $.each(userSet, function (index, user) {
                rows += `
                    <tr>
                        <td width="80%" class="text-bold-500">${user.name}</td>
                        <td width="20%" style="text-align: center">
                            <button type="button" class="btn btn-sm btn-success rounded-pill" onclick="removeUser(${user.id}, 'user')">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </td>
                    </tr>`;
                // button = `
                //     <button type="button" class="btn btn-success ml-1" onclick="teamModal(${user.id}, '${user.name}')" id="submitButton">
                //             <i class="fa-solid fa-pen"></i>
                //             Edit
                //     </button>`;
            });
            // $('#footerTeam').html(button);
        } else {
            rows += `
                <tr>
                    <td colspan="2" class="text-center">Tidak Ada User Yang Tersisa</td>
                </tr>`;
        }
        $('#table_set').html(rows);
    }

    function renderTeam(){
        let rows = "";
        $('#table_fix').empty();
        if(teamFix.length > 0){
            $.each(teamFix, function (index, user) {
                rows += `
                    <tr>
                        <td width="80%" class="text-bold-500">${user.name}</td>
                        <td width="20%" style="text-align: center">
                            <button type="button" class="btn btn-sm btn-danger rounded-pill" onclick="removeUser(${user.id}, 'team')">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </td>
                    </tr>`;
            });
        } else {
            rows += `
                <tr>
                    <td colspan="2" class="text-center">Tidak Member di Aktivitas ini</td>
                </tr>`;
        }
        $('#table_fix').html(rows);
    }

    $('#userSearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#table_set tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $('#teamSearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#table_fix tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    function removeUser(id, status){
        const user = userSet.find(user => user.id === id);
        const team = teamFix.find(user => user.id === id);

        if (status === 'team' && team) {
            teamFix = teamFix.filter(user => user.id !== id);
            if (team) userSet.push(team);
        } else if (status === 'user' && user) {
            userSet = userSet.filter(user => user.id !== id);
            if (user) teamFix.push(user);
        }

        renderUser();
        renderTeam();
    }

    $('#teamModal').on('hidden.bs.modal', function () {
        exit();
    });
</script>



@endsection