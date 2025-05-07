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
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Daftar Proyek</h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('project.create') }}" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-plus"></i> <span class="d-none d-md-inline-block">Tambah</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $page = $results && $results->perPage() ? $results->perPage() : null;
                    @endphp
                    <div class="row">
                        <form method="GET" action="{{ route('project.index') }}" id="pagination-form" class="col-12 col-lg-1">
                            <fieldset class="form-group" style="width: 70px">
                                <select class="form-select" id="entire-page" name="per_page" onchange="document.getElementById('pagination-form').submit();">
                                    <option value="5" {{ $page == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $page == 10 ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ $page == 15 ? 'selected' : '' }}>15</option>
                                    <option value="20" {{ $page == 20 ? 'selected' : '' }}>20</option>
                                </select>
                            </fieldset>
                        </form>
                        <form method="POST" action="{{ route('project.filter') }}" id="search-form" class="mb-4 col-12 col-lg-11">
                            @csrf
                            <div class="row">
                                <div class="col-lg-3 col-3">
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="start_date" value="{{ session()->has('start_date') ? session('start_date') : '' }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-3">
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="end_date" value="{{ session()->has('end_date') ? session('end_date') : '' }}">
                                    </div>
                                </div>
                                <div class="col-lg-5 col-5">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="q" value="{{ session()->has('q') ? session('q') : '' }}" placeholder="Ketik Nama Aktivitias & Klik Enter ..." onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.form.submit(); }">
                                        <button class="btn btn-primary" type="submit" id="button-addon1"><i class="fa-solid fa-magnifying-glass"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-1">
                                    <a href="{{ route('project.reset') }}" class="btn btn-secondary" type="button" id="button-addon2">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="table">
                            <thead>
                                <tr>
                                    <th width="12%" class="text-center">Mulai</th>
                                    <th width="12%" class="text-center">Selesai</th>
                                    <th>Nama Proyek</th>
                                    <th width="20%">Nama Perusahaan</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="18%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                {{-- @php
                                    $no = is_object($results) && method_exists($results, 'firstItem') ? $results->firstItem() : 0;
                                @endphp --}}
                            @if(is_object($results) && method_exists($results, 'firstItem'))
                                @foreach ($results as $project)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($project['start_date'])->translatedFormat('d-m-Y') }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($project['end_date'])->translatedFormat('d-m-Y') }}</td>
                                        <td>{{ $project['name'] }}</td>
                                        <td>{{ $project['company_name'] }}</td>
                                        <td class="text-center">
                                            {{-- <span class="badge {{ $project['status'] ? 'bg-success' : 'bg-danger'}}"> --}}
                                            <span class="badge bg-danger">
                                                {{-- {{$project['status']}} --}}
                                                Undefined
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{-- <a href="{{ route('project.edit', $project['id']) }}" class="btn btn-sm btn-warning rounded-pill" title="Edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a> --}}
                                            <a onclick="showDetail({{ json_encode($project) }})" class="btn btn-sm btn-warning rounded-pill" data-bs-toggle="modal" data-bs-target="#detailModal">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </a>
                                            <a href="{{ route('project.doc', $project['id']) }}" class="btn btn-sm btn-info rounded-pill">
                                                <i class="fa-solid fa-file"></i>
                                            </a>
                                            <a href="{{ route('project.activity', $project['id']) }}" class="btn btn-sm btn-secondary rounded-pill">
                                                <i class="fa-solid fa-chart-line"></i>
                                            </a>
                                            <button type="button" onclick="teamModal({{ $project['id'] }}, `{{ $project['name'] }}`, `{{ $project['project_leader_name'] }}`, {{ $project['project_leader_id'] }}, ``)" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#teamModal">
                                                <i class="fa-solid fa-user-group"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data</td>
                                    </tr>
                            @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    @if (is_object($results) && method_exists($results, 'onEachSide'))
                                        <td colspan="6"><span style="margin-top: 15px;">{{ $results->appends(request()->query())->links() }}</span></td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade text-left w-100" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Detail Proyek</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="closeDetailModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-8">
                            <label><b> Nama Proyek : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="project_name_detail"></p>
                            </div>
                            <label><b> Nama Perusahaan : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="company_name_detail"></p>
                            </div>
                            <label><b> Alamat Perusahaan : </b></label>
                            <div class="form-group">
                                <div class="form-floating">
                                    <p class="form-control-static" id="company_address_detail"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label><b> Nama Pimpinan Proyek : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="project_leader_detail"></p>
                            </div>
                            <label><b> Nama Direktur : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="director_name_detail"></p>
                            </div>
                            <label><b> No.Telp Direktur : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="director_phone_detail"></p>
                            </div>
                        </div>
                        <hr>
                        <div class="col-sm-8">
                            <label><b> Kontrak Mulai : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="start_project_detail"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label><b> Kontrak Selesai : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="end_project_detail"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-warning ml-1" id="editButton">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger ml-1" id="deleteButton">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left w-100" id="teamModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Tim Proyek</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="closeTeamModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div>
                <div class="modal-body">
                    <input type="text" id="project_id" hidden>
                    <div class="row">
                        <div class="col-sm-6">
                            <label><b> Nama Proyek : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="project_name_team"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label><b> Pimpinan Proyek : </b></label>
                            <div class="form-group">
                                <p class="form-control-static" id="project_leader_team"></p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" id="teamInput" style="display: none">
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
                    <div id="teamShow" style="display: none">
                        <div class="row">
                            <p class="text-center"><b>Anggota</b></p>
                            <hr>
                            <div class="col-sm-12">
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
                    {{-- <button type="button" class="btn btn-success ml-1" onclick="saveTeam()" id="submitButton">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan
                    </button> --}}
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

<div id="modernImageModal" class="modern-modal" style="display: none" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modern-modal-content">
        <img id="modernImagePreview" alt="Preview">
    </div>
    <span class="closeImage" onclick="closeModernModal()">&times;</span>
</div>

<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
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
    let projects = {!! isset($results) ? json_encode($results) : '[]' !!};
    let users = {!! isset($users) ? json_encode($users) : '[]' !!};
    let teams = {!! isset($groupedTeams) ? json_encode($groupedTeams) : '[]' !!};
    let userSet = [];
    let teamFix = [];


    document.addEventListener("DOMContentLoaded", function () {
        setUser();
        console.log(JSON.stringify(@json(session('lastRoute')), null, 2));
    });

    $('#teamModal').on('hidden.bs.modal', function () {
        $('#userSearch').val('');
        $('#teamSearch').val('');
        $('#table_set').empty();
        $('#table_fix').empty();
        userSet = [];
        setUser();
        teamFix = [];
    });

    function teamModal(id, projectName, projectLeader, projectLeaderId, status){
        teams.forEach(function (team) {
            if (team.project_id == id) {
                teamFix = team.members;
            }
        });

        userSet = userSet.filter(user => !teamFix.some(team => team.id === user.id));
        userSet = userSet.filter(user => user.id !== projectLeaderId);

        renderTeam();
        renderUser();

        $('#project_id').val(id);
        $('#project_name_team').text(projectName);
        $('#project_leader_team').text(projectLeader);
        if(teamFix.length > 0 && status === ""){
            $('#footerTeam').empty();
            let html = '';
            $.each(teamFix, function (index, user) {
                html += `
                    <tr>
                        <td width="10%" class="text-bold-500">${index+1}</td>
                        <td width="90%" class="text-bold-500">${user.name}</td>
                    </tr>`;
            });
            $('#table_show').html(html);
            $('#teamShow').show();
            $('#teamInput').hide();
        }else{
            $('#teamShow').hide();
            $('#teamInput').show();
        }

        let footerHtml = '';
        if(status === "input" || teamFix.length === 0){
            footerHtml += `
                <button type="button" class="btn btn-success ml-1" onclick="saveTeam()" id="submitButton">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan
                </button>
            `;
        }else{
            footerHtml += `
                <button type="button" class="btn btn-warning ml-1" onclick="teamModal(${id}, '${projectName}', 'input')" id="submitButton">
                        <i class="fa-solid fa-pen"></i>
                        Edit
                </button>
            `;
        }
        $('#footerTeam').html(footerHtml);
        $('#userSearch').val('');
        $('#teamSearch').val('');
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

    function saveTeam(){
        // console.log(teamFix);
        buttonLoadingStart('submitButton');
        $.ajax({
            url: "{{ route('project.store.team') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                team: teamFix,
                project_id: $('#project_id').val()
            },
            success: function (response) {
                console.log(response);
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        buttonLoadingEnd('submitButton');
                        $('#closeTeamModal').click();
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        buttonLoadingEnd('submitButton');
                        $('#closeTeamModal').click();
                    });
                }
            }, error: function (xhr) {
                console.log(xhr.responseText);
            }
        })
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
            if (result.isConfirmed) {
                $('#fullPageLoader').show();
                window.location.href = url;
            }
        });
    }

    function showDetail(data){
        console.log(data);
        let onclickDelete = `confirmDelete("` + `{{ url('project/destroy/${data.id}') }}`;
        $("#project_leader_detail").text(data.project_leader_name);
        $("#project_name_detail").text(data.name);
        $("#company_name_detail").text(data.company_name);
        $("#company_address_detail").text(data.company_address);
        $("#director_name_detail").text(data.company_director_name);
        $("#director_phone_detail").text(data.company_director_phone);
        $("#start_project_detail").text(dateFormat(data.start_date));
        $("#end_project_detail").text(dateFormat(data.end_date));
        $("#editButton").attr("href", `project/form-edit/${data.id}`);
        $("#deleteButton").attr("onclick", onclickDelete);
    }

    function dateFormat(dateString) {
        let [year, month, day] = dateString.split("-");

        let date = new Date(year, month - 1, day);

        let formattedDate = new Intl.DateTimeFormat("id-ID", {
            day: "2-digit",
            month: "long",
            year: "numeric"
        }).format(date);

        return formattedDate;
    }

    function openModernModal(imageSrc) {
        document.getElementById('modernImagePreview').src = imageSrc;
        document.getElementById('modernImageModal').style.display = "flex";
    }

    function closeModernModal() {
        document.getElementById('modernImageModal').style.display = "none";
    }
</script>
@endsection