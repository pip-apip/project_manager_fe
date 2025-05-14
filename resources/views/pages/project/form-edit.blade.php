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
        table-layout: fixed;
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
        border-color: #ccc;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    /* Tab container */
    .tab {
        overflow: hidden;
        border-bottom: 2px solid #980003;
        /* background-color: #F5F5F5; */
        border-radius: 8px 8px 0 0;
        margin-bottom: 1rem;
    }

    .tab button {
        background-color: #ffffff;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 12px 24px;
        transition: 0.3s;
        font-size: 16px;
        color: #333;
        border-right: 1px solid #ccc;
    }

    .tab button:hover {
        background-color: #d9d9d9;
    }

    .tab button.active {
        background-color: #980003;
        color: #fff;
        font-weight: bold;
    }

    .tabcontent {
        display: none;
        /* border: 1px solid #ccc; */
        /* border-top: none; */
        background-color: #fff;
        /* border-radius: 0 0 8px 8px; */
    }
</style>

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Ubah <span class="d-none d-md-inline-block">Proyek</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('project.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-sm-12 col-12 tab">
                        <button class="tablink active" onclick="openTab(event, 'formProject')">Info. Umum</button>
                        <button class="tablink" onclick="openTab(event, 'form2')">Anggota Proyek</button>
                        {{-- <button class="tablink" onclick="openTab(event, 'formTeams')">Team Proyek</button> --}}
                    </div>

                    {{-- Form Section 1 Proyek --}}
                    <div class="tabcontent" id="formProject" style="margin-bottom: 1rem; display: block">
                        <form action="{{ route('project.update', $project['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Kode Proyek <code>*</code></label>
                                </div>
                                <div class="form-group col-md-10">
                                    <input type="text" placeholder="Masukkan Kode Proyek" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $project ? $project['code'] : '') }}" autocomplete="off" />
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>Nama Proyek <code>*</code></label>
                                </div>
                                <div class="form-group col-md-10">
                                    <input type="text" placeholder="Masukkan Nama Proyek" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $project ? $project['name'] : '') }}" autocomplete="off" />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>PT Pemenang <code>*</code></label>
                                </div>
                                <fieldset class="form-group col-md-10">
                                    <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id">
                                        <option value="">Pilih PT Pemenang</option>
                                        @foreach ($companies as $comp)
                                        <option value="{{ $comp['id'] }}" {{ old('company_id', $project ? $project['company_id'] : '') == $comp['id'] ? 'selected' : '' }}>
                                            {{ $comp['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </fieldset>

                                <div class="col-md-2">
                                    <label>Nilai Proyek <code>*</code></label>
                                </div>
                                <div class="form-group col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">Rp.</span>
                                        <input type="text" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ old('value', $project ? $project['value'] : '') }}" autocomplete="off" placeholder="Masukkan Nilai Proyek"/>
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label>Klien <code>*</code></label>
                                </div>
                                <div class="form-group col-md-10">
                                    <input type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ old('client', $project ? $project['client'] : '') }}" autocomplete="off" placeholder="Masukkan Nama Klien"/>
                                    @error('client')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>Kontrak Mulai <code>*</code></label>
                                </div>
                                <div class="form-group col-md-10">
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date', $project ? $project['start_date'] : '') }}" autocomplete="off" />
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>Kontrak Selesai <code>*</code></label>
                                </div>
                                <div class="form-group col-md-10">
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date', $project ? $project['end_date'] : '') }}" autocomplete="off" />
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>Akhir Pemeliharaan <code>*</code></label>
                                </div>
                                <div class="form-group col-md-10">
                                    <input type="date" class="form-control @error('maintenance_date') is-invalid @enderror" name="maintenance_date" value="{{ old('maintenance_date', $project ? $project['maintenance_date'] : '') }}" autocomplete="off" />
                                    @error('maintenance_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                    <button type="submit" class="btn btn-primary me-1 mb-1" id="submitButtonPage1">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Form Section 2 Anggota Proyek --}}
                    <div class="tabcontent" id="form2" style="margin-bottom: 1rem; display: none">
                        <form action="{{ route('project.update', $project['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Pimpinan Proyek /PL <code>*</code></label>
                                </div>
                                <fieldset class="form-group col-md-10">
                                    <select class="form-select @error('project_leader_id') is-invalid @enderror" id="project_leader_id" name="project_leader_id">
                                        <option value="">Pilih Pimpinan Proyek</option>
                                        @foreach ($users as $user)
                                        <option value="{{ $user['id'] }}" {{ old('project_leader_id', $project['project_leader_id'] ?? '') == $user['id'] ? 'selected' : '' }}>
                                            {{ $user['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('project_leader_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </fieldset>

                                <div class="col-md-2">
                                    <label>PPK <code>*</code></label>
                                </div>
                                <div class="form-group col-md-10">
                                    <input type="text" class="form-control @error('ppk') is-invalid @enderror" name="ppk" value="{{ old('ppk', $project ? $project['ppk'] : '') }}" autocomplete="off" placeholder="Masukkan Nama PPK"/>
                                    @error('ppk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label>Tim Pendukung <code>*</code></label>
                                </div>
                                <div class="form-group col-md-4">
                                    <input type="text" class="form-control" id="supporting_team" placeholder="Masukkan Nama Tim Pendukung" />
                                    <input type="text" id="supporting_team_collection" name="support_teams" style="display: none">
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-primary" id="addSupportingTeam">Tambah</button>
                                </div>

                                <div class="col-md-6 offset-md-2">
                                    <table class="table table-striped mb-0 scrollable-table">
                                        <thead>
                                            <tr>
                                                <td width="10%">No</td>
                                                <td widht="80%">Tim Pendukung</td>
                                                <td width="10%">Aksi</td>
                                            </tr>
                                        </thead>
                                        <tbody id="table_supporting_team">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                <button type="submit" class="btn btn-primary me-1 mb-1" id="submitButtonPage2">Simpan</button>
                            </div>
                        </form>
                    </div>

                    {{-- Form Section 3 Team Proyek --}}
                    {{-- <div class="tabcontent" id="formTeams" style="margin-bottom: 1rem; display: none">
                        <form action="{{ route('project.update', $project['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <input type="text" id="teams" name="teams" style="display: none">
                                <div class="row" id="teamInput">
                                    <div class="col-sm-6">
                                        <p class="text-center"><b>Semua</b></p>
                                        <hr>
                                        <table class="table table-striped mb-0 scrollable-table">
                                            <thead>
                                                <tr>
                                                    <th width="80%">Nama <input type="text" id="userSearch"></th>
                                                    <th width="20%" style="text-align: center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_set">
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr class="d-sm-none">
                                    <div class="col-sm-6">
                                        <p class="text-center"><b>Anggota</b></p>
                                        <hr>
                                        <table class="table table-striped mb-0 scrollable-table">
                                            <thead>
                                                <tr>
                                                    <th width="80%">Nama <input type="text" id="teamSearch"></th>
                                                    <th width="20%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_fix">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                <button type="submit"
                                    class="btn btn-primary me-1 mb-1" id="submitButtonPage3">Simpan</button>
                            </div>
                        </form>
                    </div> --}}

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    let users = {!! isset($users) ? json_encode($users) : '[]' !!};
    let project = {!! isset($project) ? json_encode($project) : '[]' !!}
    let userSet = [];
    let teamFix = [];
    let supportTeam = [];

    document.addEventListener("DOMContentLoaded", function () {
        console.log(JSON.stringify(@json(session('lastRoute')), null, 2));
    });

    $(document).ready(function() {
        $('form').on('submit', function() {
            buttonLoadingStart('submitButton');
        });
        if(project['support_teams']){
            for(let i = 0; i < project['support_teams'].length; i++){
                supportTeam.push(project['support_teams'][i]);
                // console.log(project['support_teams'][i]);
            }
        }
        setUser();
        renderSupportTeam();
    });

    $('#userSearch').on('keyup', function () {
        let value = $(this).val().toLowerCase();
        let visibleCount = 0;

        $('#table_set tr').each(function () {
            const isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(isMatch);
            if (isMatch) visibleCount++;
        });

        $('#table_set .no-result').remove();

        if (visibleCount === 0) {
            $('#table_set').append(`
                <tr style="background-color: #F3F3F2;">
                    <td colspan="2" class="text-center">Tidak Ada User Yang Dicari</td>
                </tr>
            `);
        }
    });

    $('#teamSearch').on('keyup', function () {
        let value = $(this).val().toLowerCase();
        let visibleCount = 0;

        $('#table_fix tr').each(function () {
            const isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(isMatch);
            if (isMatch) visibleCount++;
        });

        $('#table_fix .no-result').remove();

        if (visibleCount === 0) {
            $('#table_fix').append(`
                <tr style="background-color: #F3F3F2;">
                    <td colspan="2" class="text-center">Tidak Ada User Yang Dicari</td>
                </tr>
            `);
        }
    });

    function setUser(){
        $.each(users, function (index, user) {
            userSet.push({
                name: user.name,
                id: user.id
            });
        });
        renderUser();
        renderTeam();
    }

    function renderUser(){
        let rows = "";
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
            });
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
                    <td colspan="2" class="text-center">Tidak Anggota di Proyek ini</td>
                </tr>`;
        }
        $('#table_fix').html(rows);
    }

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

        $('#userSearch').val('');
        $('#teamSearch').val('');

        $('#teams').val(JSON.stringify(teamFix));
    }

    $('#addSupportingTeam').on('click', function (e) {
        e.preventDefault();
        const teamName = $('#supporting_team').val();
        if (teamName) {
            supportTeam.push(teamName);
            $('#supporting_team').val('');
            renderSupportTeam();
        }
    });

    function renderSupportTeam() {
        let rows = "";
        $('#table_supporting_team').empty();
        if (supportTeam.length > 0) {
            $.each(supportTeam, function (index, team) {
                rows += `
                    <tr>
                        <td width="10%" class="text-bold-500">${index + 1}</td>
                        <td width="80%" class="text-bold-500">${team}</td>
                        <td width="10%" style="text-align: center">
                            <button type="button" class="btn btn-sm btn-danger rounded-pill" onclick="removeSupportTeam(${index})">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </td>
                    </tr>`;
            });
        } else {
            rows += `
                <tr>
                    <td colspan="2" class="text-center">Tidak Ada Tim Pendukung</td>
                </tr>`;
        }
        $('#table_supporting_team').html(rows);
        $('#supporting_team_collection').val(JSON.stringify(supportTeam));
    }

    function removeSupportTeam(index) {
        supportTeam.splice(index, 1);
        renderSupportTeam();
    }

    // Format input nilai_project
    const input = document.querySelector('input[name="value"]');

    input.addEventListener('input', function (e) {
        let value = this.value.replace(/[^\d]/g, '');
        if (!value) {
            this.value = '';
            return;
        }

        this.value = formatRupiah(value);
    });

    function formatRupiah(angka) {
        let numberString = angka.toString();
        let sisa = numberString.length % 3;
        let rupiah = numberString.substr(0, sisa);
        let ribuan = numberString.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return rupiah;
    }

    const form = document.getElementById('form');
    form.addEventListener('submit', function () {
        const raw = input.value.replace(/[^0-9]/g, '');
        input.value = raw;
    });

    // Tab Functionality
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
</script>

@endsection
