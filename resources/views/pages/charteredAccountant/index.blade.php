@extends('layouts.app')

@section('title', 'Activity Page')

@section('content')

<div class="page-heading">
    <div class="page-content">
        <section class="section">
            <div class="row">
                <div class="col-sm-12" id="tagsSearch">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-8 col-8">
                                    <h1>Data CA</h1>
                                </div>
                                <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                                    <a href="{{ route('ca.create') }}" class="btn btn-success btn-sm">
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
                                            <th width="15%" class="text-center">Bukti Dokumen</th>
                                            <th width="10%" class="text-center">Aksi</th>
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
                                                <a href="{{ route('activity.doc', $ca['id']) }}" class="btn btn-sm btn-info rounded-pill">
                                                    <i class="fa-solid fa-file"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('activity.edit', $ca['id']) }}" class="btn btn-sm btn-warning rounded-pill">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger rounded-pill" onclick="confirmDelete('{{ route('activity.destroy',$ca['id']) }}')">
                                                    <i class="fa-solid fa-trash"></i>
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

    let lastPage = {!! json_encode(session('lastUrl') ) !!}

</script>
@endsection
