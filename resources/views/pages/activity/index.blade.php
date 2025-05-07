@extends('layouts.app')

@section('title', 'Project Page')

@section('content')

<div class="page-heading">
    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Daftar Aktivitas</h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('activity.create') }}" class="btn btn-success btn-sm">
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
                        <form method="GET" action="{{ route('activity.index') }}" id="pagination-form" class="col-12 col-lg-1">
                            <fieldset class="form-group" style="width: 70px">
                                <select class="form-select" id="entire-page" name="per_page" onchange="document.getElementById('pagination-form').submit();">
                                    <option value="5" {{ $page == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $page == 10 ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ $page == 15 ? 'selected' : '' }}>15</option>
                                    <option value="20" {{ $page == 20 ? 'selected' : '' }}>20</option>
                                </select>
                            </fieldset>
                        </form>
                        <form method="POST" action="{{ route('activity.filter') }}" id="search-form" class="mb-4 col-12 col-lg-11">
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
                                    <a href="{{ route('activity.reset') }}" class="btn btn-secondary" type="button" id="button-addon2">Reset</a>
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
                                    <th width="20%">Proyek</th>
                                    <th>Judul Aktivitas</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                            @if(is_object($results) && method_exists($results, 'firstItem'))
                                @foreach ($results as $act)
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($act['start_date'])->translatedFormat('d-m-Y') }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($act['end_date'])->translatedFormat('d-m-Y') }}</td>
                                    <td>{{ $act['project_name'] }}</td>
                                    <td>{{ $act['title'] }}</td>
                                    <td class="text-center">
                                        {{-- <span class="badge {{ $project['status'] ? 'bg-success' : 'bg-danger'}}"> --}}
                                        <span class="badge bg-danger">
                                            {{-- {{$project['status']}} --}}
                                            Undefined
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <!-- <a href="{{ route('activity.edit', $act['id']) }}" class="btn btn-sm btn-warning rounded-pill">
                                            <i class="fa-solid fa-pen"></i>
                                        </a> -->
                                        <a href="{{ route('activity.doc', $act['id']) }}" class="btn btn-sm btn-info rounded-pill">
                                            <i class="fa-solid fa-file"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-danger rounded-pill" onclick="confirmDelete('{{ route('activity.destroy', $act['id']) }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
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
            timer: 2000
        }).then(() => {
            $('#fullPageLoader').hide();
        });
    </script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // const table = document.querySelector('#table');

        // new simpleDatatables.DataTable(table, {
        //     perPage: 5,
        //     perPageSelect: [5, 10, 25, 50]
        // });
    });

    $('#search-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // beforeSend: function() {
            //     // Optional: tampilkan loading
            //     $('#activity-results').html('<p>Loading...</p>');
            // },
            success: function (response) {
                if (response.status === 'success' && response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    alert('Gagal memproses filter.');
                }
            },
            error: function (xhr) {
                alert('Terjadi kesalahan saat memproses filter');
                console.log(xhr.responseText);
            }
        });
    });

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
</script>

@endsection