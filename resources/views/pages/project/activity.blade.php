@extends('layouts.app')

@section('title', 'Activity Page')

@section('content')

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Detail <span class="d-none d-md-inline-block">Proyek</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route(session('lastRoute')) }}" class="btn btn-secondary btn-sm">
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
                                <input type="text" class="form-control" value="{{ $project['name'] }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Nama Klien</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ $project['client'] }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Nama PPK</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ $project['ppk'] }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Nama PT Pemenang</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ $project['company_name'] }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Kontrak Mulai</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($project['start_date'])->translatedFormat('d-m-Y') }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Kontrak Selesai</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($project['end_date'])->translatedFormat('d-m-Y') }}" readonly />
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
                            <div class="row">
                                <div class="col-sm-8 col-8">
                                    <h1 class="card-title">Aktivitas Proyek</h1>
                                </div>
                                <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                                    <a href="{{ route('activity.create', ['project_id' => $project['id']]) }}" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-plus"></i> <span class="d-none d-md-inline-block">Tambah</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table1">
                                    <thead>
                                        <tr>
                                            <th width="12%" class="text-center">Mulai</th>
                                            <th width="12%" class="text-center">Selesai</th>
                                            <th>Catatan Aktivitas</th>
                                            <th width="10%" class="text-center">Status</th>
                                            <th width="18%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table">
                                    @if ($activities !== [])
                                        @foreach ($activities as $act)
                                        @php
                                            $badge_bg = '';
                                            if($act['status'] == 'DONE'){
                                                $badge_bg = 'bg-success';
                                            }else if($act['status'] == 'ON PROGRESS'){
                                                $badge_bg = 'bg-info';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($act['start_date'])->translatedFormat('d-m-Y') }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($act['end_date'])->translatedFormat('d-m-Y') }}</td>
                                            <td>{{ $act['title'] }}</td>
                                            <td class="text-center">
                                                @if(session('user.role') == 'SUPERADMIN')
                                                <button class="badge {{ $badge_bg }} border-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    {{ $act['status'] ?? 'Undefined' }}
                                                </button>
                                                <div class="dropdown-menu" style="">
                                                    <button class="dropdown-item {{ $act['status'] == 'DONE' ? 'active' : '' }}" onclick="changeStatus({{ $act['id'] }}, 'DONE')">DONE</button>
                                                    <button class="dropdown-item {{ $act['status'] == 'ON PROGRESS' ? 'active' : '' }}" onclick="changeStatus({{ $act['id'] }}, 'ON PROGRESS')">ON PROGRESS</button>
                                                </div>
                                                @else
                                                <span class="badge {{ $badge_bg }}">
                                                    {{$act['status']}}
                                                </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('activity.edit', $act['id']) }}" class="btn btn-sm btn-warning rounded-pill">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger rounded-pill" onclick="confirmDelete('{{ route('activity.destroy', $act['id']) }}')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                                <a href="{{ route('activity.doc', $act['id']) }}" class="btn btn-sm btn-info rounded-pill">
                                                    <i class="fa-solid fa-file"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada aktivitas</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


{{-- JQuery --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    let projectData = {!! json_encode($project) !!}
    let activityList = {!! json_encode($activities) !!}
    let lastPage = {!! json_encode(session('lastUrl') ) !!}

    function changeStatus(id, status){
        $.ajax({
            url: "{{ env('API_BASE_URL') }}/activities/" + id,
            type: "PATCH",
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + @json(session('user.access_token')),
            },
            data: {
                status: status
                // _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                // console.log(response);
                if (response.status == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            }
        })
    }

</script>
@endsection