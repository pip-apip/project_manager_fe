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

    /* Override tampilan select2 agar mirip select bawaan template */
    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        height: calc(2.5rem + 2px);
        padding: .375rem 1.75rem .375rem .75rem;
        font-size: 1rem;
        color: #495057;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.8rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.0rem;
        top: 0.25rem;
        right: 0.5rem;
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
                            <h1>{{ $status === 'create' ? 'Tambah' : 'Ubah' }} <span class="d-none d-md-inline-block">CA</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ isset($lastRoute[0], $lastRoute[1]) ? route($lastRoute[0],$lastRoute[1]) : route('activity.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($status === 'create')
                        <form action="{{ route('activity.store') }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @elseif ($status === 'edit')
                        <form action="{{ route('activity.update', $ca['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @endif
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <label>Pilih Proyek</label>
                            </div>
                            <fieldset class="form-group col-md-10">
                                <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                    <option value="">Pilih Proyek</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project['id'] }}" {{ old('project_id', $projectId ?? $ca['project_id'] ?? '') == $project['id'] ? 'selected' : '' }}>
                                            {{ $project['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </fieldset>
                            <div class="col-md-2">
                                <label>Tanggal CA <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="date" class="form-control @error('tanggal_ca') is-invalid @enderror" name="tanggal_ca" id="tanggal_ca" value="{{ old('tanggal_ca', $ca ? $ca['tanggal_ca'] : '') }}" autocomplete="off" />
                                @error('tanggal_ca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Klasifikasi <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Klasifikasi CA" class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" id="keterangan" value="{{ old('keterangan', $ca ? $ca['keterangan'] : '') }}"  autocomplete="off" />
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Keterangan CA <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Keterangan CA" class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" id="keterangan" value="{{ old('keterangan', $ca ? $ca['keterangan'] : '') }}"  autocomplete="off" />
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Nama Pemohon <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Nama Pemohon CA" class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" id="keterangan" value="{{ old('keterangan', $ca ? $ca['keterangan'] : '') }}"  autocomplete="off" />
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label>Nominal CA <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">Rp.</span>
                                    <input type="text" class="form-control @error('total') is-invalid @enderror" name="total" value="{{ old('total', $ca ? $ca['total'] : '') }}" autocomplete="off" placeholder="Masukkan Nominal CA"/>
                                    @error('total')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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

<div id="fullPageLoader" class="full-page-loader" style="display: none">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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