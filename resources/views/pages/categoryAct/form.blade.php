@extends('layouts.app')

@section('title', 'Project Page')

@section('content')

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>{{ $status === 'create' ? 'Tambah' : 'Edit' }} <span class="d-none d-md-inline-block">Kategori Aktivitas</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('categoryAct.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($status === 'create')
                        <form action="{{ route('categoryAct.store') }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @elseif ($status === 'edit')
                        <form action="{{ route('categoryAct.update', $category['id']) }}" method="POST" class="form form-vertical" enctype="multipart/form-data">
                    @endif
                    @csrf
                        {{-- {{ dd($category); }} --}}
                        <div class="row">
                            <div class="col-md-2">
                                <label>Proyek <code>*</code></label>
                            </div>
                            <fieldset class="form-group col-md-10">
                                <select class="form-select" id="project_id" name="project_id">
                                    <option value="#">Pilih Proyek</option>
                                    @foreach ($projects as $project)
                                    <option value="{{ $project['id'] }}" {{ old('project_id', $category ? $category['project_id'] : '') == $project['id'] ? 'selected' : '' }}>
                                        {{ $project['name'] }}
                                    </option>
                                    @endforeach
                                    <option value="0">Lain - lain</option>
                                </select>
                            </fieldset>
                            <div class="col-md-2">
                                <label>Nama Kategori <code>*</code></label>
                            </div>
                            <div class="form-group col-md-10">
                                <input type="text" placeholder="Masukkan Nama Kategori" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $category ? $category['name'] : '') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-12 offset-sm-2 d-flex justify-content-start mt-3">
                                <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                                <button type="reset" class="btn btn-light-secondary me-1 mb-1">Batal</button>
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
        });
    });
</script>



@endsection