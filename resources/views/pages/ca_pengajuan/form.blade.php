@extends('layouts.app')

@section('title', 'Project Page')

@section('content')

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
                            <h1>{{ $status === 'create' ? 'Tambah' : 'Ubah' }} <span class="d-none d-md-inline-block">Pengajuan CA</span></h1>
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
                                <label>Nama CA <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Nama CA" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $ca ? $ca['name'] : '') }}"  autocomplete="off" />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                                <label>Item CA <code>*</code></label>
                            </div>
                            <div class="form-group col-md-8 d-flex align-items-center">
                                <input type="text" placeholder="Keterangan Item" class="form-control me-2" id="keterangan_ca"  autocomplete="off" />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <button class="btn btn-primary me-2" type="button" onclick="minusQuantity()"><i class="fa-solid fa-minus"></i></button>
                                <span class="me-2" id="quantity_ca">0</span>
                                <button class="btn btn-primary me-2" type="button" onclick="plusQuantity()"><i class="fa-solid fa-plus"></i></button>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">Rp.</span>
                                    <input type="text" class="form-control" name="value" id="total_ca" autocomplete="off" placeholder="Masukkan Nominal CA"/>
                                    @error('total')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button onclick="AddedItem()" type="button" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-plus"></i> <span class="d-none d-md-inline-block">Tambah</span>
                                </button>
                            </div>

                            <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                <button type="submit"
                                    class="btn btn-primary me-1 mb-1" id="submitButton">Simpan</button>
                                <button type="reset"
                                    class="btn btn-light-secondary me-1 mb-1">Batal</button>
                            </div>
                            {{-- <div class="col-md-2">
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
                            </div> --}}
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1 class="card-title">Items CA</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table1">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th class="text-center">Keterangan</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-center">Nominal</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table">
                                            <td colspan="5" class="text-center">Tidak ada item CA</td></td>
                                        </tbody>
                                    </table>
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
    let items = [];

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

        // Format on load
        if (input.value) {
            const raw = input.value.replace(/[^0-9]/g, '');
            input.value = formatRupiah(raw);
        }

        if ($('#table').children().length === 0) {
            $('#table').append(`
                <tr>
                    <td colspan="5" class="text-center">Tidak ada item CA</td>
                </tr>
            `);
        }
    });

    function plusQuantity() {
        let quantityElement = $('#quantity_ca');
        let currentQuantity = parseInt(quantityElement.text());
        quantityElement.text(currentQuantity + 1);
    }

    function minusQuantity() {
        let quantityElement = $('#quantity_ca');
        let currentQuantity = parseInt(quantityElement.text());
        if (currentQuantity > 0) {
            quantityElement.text(currentQuantity - 1);
        }
    }

    function clearItemsForm(){
        $('#keterangan_ca').val('');
        $('#quantity_ca').text('0');
        $('#total_ca').val('');
    }

    function AddedItem(){
        let keterangan = $('#keterangan_ca').val();
        let quantity = $('#quantity_ca').text();
        let total = $('#total_ca').val();
        // console.log(keterangan, quantity, total);

        if (keterangan === '' || quantity === '0' || total === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Semua field harus diisi!',
            });
            return;
        }
        let item = {
            keterangan: keterangan,
            quantity: quantity,
            total: total
        };
        items.push(item);
        renderItems();
        clearItemsForm();
    }

    function renderItems() {
        let tableBody = $('#table');
        tableBody.empty();

        let row = '';
        items.forEach((item, index) => {
            row += `<tr>
                <td>${index + 1}</td>
                <td class="text-center">${item.keterangan}</td>
                <td class="text-center">${item.quantity}</td>
                <td class="text-center">Rp. ${item.total}</td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm" onclick="removeItem(${index})"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>`;
        });
        let sumTotal = items.reduce((sum, item) => {
            let total = parseInt(item.total.replace(/[^0-9]/g, '')) || 0;
            let quantity = parseInt(item.quantity) || 0;
            return sum + (total * quantity);
        }, 0);
        row += `<tr>
            <td colspan="3" class="text-center">Total Nominal : </td>
            <td class="text-center">Rp. ${formatRupiah(sumTotal.toString())}</td>
            <td></td>
        </tr>`;
        tableBody.append(row);
    }

    function removeItem(index) {
        items.splice(index, 1);
        renderItems();
    }

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
</script>

@endsection
