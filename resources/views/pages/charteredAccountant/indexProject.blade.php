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
                            <h1>Daftar Proyek</h1>
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
                                        <input type="text" class="form-control" name="q" value="{{ session()->has('q') ? session('q') : '' }}" placeholder="Ketik Nama Proyek & Klik Enter ..." onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.form.submit(); }">
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
                                    <th width="5%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                {{-- @php
                                    $no = is_object($results) && method_exists($results, 'firstItem') ? $results->firstItem() : 0;
                                @endphp --}}
                            @if(is_object($results) && method_exists($results, 'firstItem'))
                                @foreach ($results as $project)
                                @php
                                    $badge_bg = '';
                                    if($project['status'] == 'WAITING'){
                                        $badge_bg = 'bg-warning';
                                    }else if($project['status'] == 'ON PROGRESS'){
                                        $badge_bg = 'bg-info';
                                    }
                                    else if($project['status'] == 'CLOSED'){
                                        $badge_bg = 'bg-success';
                                    }
                                @endphp
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($project['start_date'])->translatedFormat('d-m-Y') }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($project['end_date'])->translatedFormat('d-m-Y') }}</td>
                                        <td>{{ $project['name'] }}</td>
                                        <td>{{ $project['company_name'] }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $badge_bg }}">
                                                {{$project['status']}}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('ca.project.detail', $project['id']) }}" class="btn btn-sm btn-info rounded-pill">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
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

@endsection