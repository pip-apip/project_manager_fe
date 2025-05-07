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
                            <a href="{{ route('project.index') }}" class="btn btn-secondary btn-sm">
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
                                <label>Nama Perusahaan</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ $project['company_name'] }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Kontrak Mulai</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($project['start_date'])->translatedFormat('d F Y') }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Kontrak Selesai</label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($project['end_date'])->translatedFormat('d F Y') }}" readonly />
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
                                            <th width="13%" class="text-center">Mulai</th>
                                            <th width="13%" class="text-center">Selesai</th>
                                            <th>Judul Aktivitas</th>
                                            <th width="10%" class="text-center">Status</th>
                                            <th width="18%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table">
                                    @if ($activities !== [])
                                        @foreach ($activities as $act)
                                        <tr>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($act['start_date'])->translatedFormat('d F Y') }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($act['end_date'])->translatedFormat('d F Y') }}</td>
                                            <td>{{ $act['title'] }}</td>
                                            <td class="text-center">
                                                {{-- <span class="badge {{ $project['status'] ? 'bg-success' : 'bg-danger'}}"> --}}
                                                <span class="badge bg-danger">
                                                    {{-- {{$project['status']}} --}}
                                                    Undefined
                                                </span>
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

</script>
@endsection