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
                            <a href="{{ route('ca.project') }}" class="btn btn-secondary btn-sm">
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
                                    <h1 class="card-title">CA Proyek</h1>
                                </div>
                                <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                                    <a href="{{ route('ca.create', ['project_id' => $project['id']]) }}" class="btn btn-success btn-sm">
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
                                            <th width="12%" class="text-center">Tanggal</th>
                                            <th width="12%" class="text-center">Pemohon</th>
                                            <th>Klasifikasi</th>
                                            <th>Keterangan</th>
                                            <th width="10%" class="text-center">Grand Total</th>
                                            <th width="18%" class="text-center">Bukti Dokumen</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table">
                                    @if ($caData !== [])
                                        @foreach ($caData as $ca)
                                        <tr>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($ca['tanggal_ca'])->translatedFormat('d-m-Y') }}</td>
                                            <td>{{ $ca['nama_pemohon'] }}</td>
                                            <td>{{ $ca['klasifikasi'] }}</td>
                                            <td>{{ $ca['keterangan'] }}</td>
                                            <td>{{ $ca['total_ca'] }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('activity.edit', $ca['id']) }}" class="btn btn-sm btn-warning rounded-pill">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger rounded-pill" onclick="confirmDelete('{{ route('activity.destroy',$ca['id']) }}')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                                <a href="{{ route('activity.doc', $ca['id']) }}" class="btn btn-sm btn-info rounded-pill">
                                                    <i class="fa-solid fa-file"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada CA</td>
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

    let lastPage = {!! json_encode(session('lastUrl') ) !!}

</script>
@endsection
