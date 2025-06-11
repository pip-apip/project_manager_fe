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

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="progress progress-danger progress-sm  mb-4">
                                <div class="progress-bar" id="progress" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-sm-8 col-8">
                            <h1>{{ $status === 'create' ? 'Tambah' : 'Ubah' }} <span class="d-none d-md-inline-block">Proyek</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('project.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                        <form action="{{ route('project.store') }}" method="POST" class="form form-vertical" enctype="multipart/form-data" id="form">
                    @csrf
                        {{-- Form Section 1 Proyek --}}
                        <div class="row" id="formProject">
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
                                <label>Nilai Kontrak <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">Rp.</span>
                                    <input type="text" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ old('value', $project ? $project['value'] : '') }}" autocomplete="off" placeholder="Masukkan Nilai Kontrak"/>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label>No Surat Perjanjian Kontrak <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control @error('contract_number') is-invalid @enderror" name="contract_number" value="{{ old('contract_number', $project ? $project['contract_number'] : '') }}" autocomplete="off" placeholder="Masukkan No Surat Perjanjian Kontrak"/>
                                @error('contract_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label>Tanggal Surat Perjanjian Kontrak <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="date" class="form-control @error('contract_date') is-invalid @enderror" name="contract_date" value="{{ old('contract_date', $project ? $project['contract_date'] : '') }}" autocomplete="off"/>
                                @error('contract_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                        </div>

                        {{-- Form Section 2 --}}

                        <div class="row" id="form2" style="margin-bottom: 1rem">
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

                        {{-- Form Section 3 Teams --}}
                        <div class="row" id="formTeams" style="margin-bottom: 1rem">
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
                        <div class="row">
                            <div class="col-sm-12 mt-2"></div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-secondary me-1 mb-1" id="backButton">Kembali</button>
                            </div>
                            <div class="col-sm-8 justify-content-center d-flex">
                                <p class="form-control-static" style="color: grey; opacity: 0.5;" id="section-description"></p>
                            </div>
                            <div class="col-sm-2 justify-content-end d-flex">
                                <button type="button" class="btn btn-secondary me-1 mb-1" id="nextButton">Selanjutnya</button>
                                <div id="actionButton">
                                    <button type="submit" class="btn btn-primary me-1 mb-1" id="submitButton">Simpan</button>
                                    {{-- <button type="reset" class="btn btn-light-secondary me-1 mb-1">Batal</button> --}}
                                </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    let step = 1;
    let users = {!! isset($users) ? json_encode($users) : '[]' !!};
    let userSet = [];
    let teamFix = [];
    let supportTeam = [];

    // document.addEventListener("DOMContentLoaded", function () {
    //     console.log(JSON.stringify(@json(session('lastRoute')), null, 2));
    // });

    $(document).ready(function() {
        $('form').on('submit', function() {
            // $('#fullPageLoader').show();
            buttonLoadingStart('submitButton');
        });
        setUser();
        showContent();
        renderSupportTeam();
    });

    $('#nextButton').on('click', function () {
        step++;
        showContent();
    });
    $('#backButton').on('click', function () {
        step--;
        showContent();
    });

    function showContent() {
        if (step === 1) {
            $('#section-description').text('DETAIL PROJECT - (1/3)');
            $('#formProject').show();
            $('#nextButton').show();
            $('#formTeams, #form2, #backButton, #actionButton').hide();
            $('#progress').css('width', '33.3%');
            $('#progress').attr('aria-valuenow', '33.3');
        } else if (step === 2) {
            $('#section-description').text('DETAIL PROYEK - (2/3)');
            $('#nextButton, #backButton, #form2').show();
            $('#formProject, #formTeams, #actionButton').hide();
            $('#progress').css('width', '66.6%');
            $('#progress').attr('aria-valuenow', '66.6');
        } else if (step === 3) {
            $('#section-description').text('ANGGOTA PROYEK - (3/3)');
            $('#formTeams, #actionButton, #backButton').show();
            $('#formProject, #form2, #nextButton').hide();
            $('#progress').css('width', '100%');
            $('#progress').attr('aria-valuenow', '100');
        }
    }

    $('#userSearch').on('keyup', function () {
        let value = $(this).val().toLowerCase();
        let visibleCount = 0;

        $('#table_set tr').each(function () {
            const isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(isMatch);
            if (isMatch) visibleCount++;
        });

        // Hapus pesan kosong sebelumnya
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

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('input[name="value"]');
        const form = document.getElementById('form');

        input.addEventListener('input', function () {
            let value = this.value.replace(/[^\d]/g, '');
            this.value = value ? formatRupiah(value) : '';
        });

        form.addEventListener('submit', function () {
            const raw = input.value.replace(/[^0-9]/g, '');
            input.value = raw;
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

        // Format on load
        if (input.value) {
            const raw = input.value.replace(/[^0-9]/g, '');
            input.value = formatRupiah(raw);
        }
    });
</script>

@endsection
