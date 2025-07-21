@extends('layouts.app')

@section('title', 'User Page')

@section('content')

    <style>
        .kanban-column {
            min-height: 300px;
            background-color: #f9f9f9;
        }

        .kanban-item {
            cursor: grab;
            margin-bottom: 8px;
        }

        .kanban-item:hover {
            background-color: #f0f0f0;
        }
    </style>
    <div class="page-heading">
        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-8 col-8">
                                <h1>Progress Kanban Projek</h1>
                            </div>
                            {{-- <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('user.create') }}" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-plus"></i> <span class="d-none d-md-inline-block">Tambah</span>
                            </a>
                        </div> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            // $page = $results && $results->perPage() ? $results->perPage() : null;
                        @endphp
                        <div class="row">
                            {{-- <form method="GET" action="{{ route('user.index') }}" id="pagination-form"
                                class="col-12 col-lg-1">
                                <fieldset class="form-group" style="width: 70px">
                                    <select class="form-select" id="entire-page" name="per_page"
                                        onchange="document.getElementById('pagination-form').submit();">
                                        <option value="5" {{ $page == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ $page == 10 ? 'selected' : '' }}>10</option>
                                        <option value="15" {{ $page == 15 ? 'selected' : '' }}>15</option>
                                        <option value="20" {{ $page == 20 ? 'selected' : '' }}>20</option>
                                    </select>
                                </fieldset>
                            </form> --}}
                            <form method="POST" action="{{ route('progress.filter') }}" id="search-form"
                                class="mb-4 col-12 col-lg-12">
                                @csrf
                                <div class="row">
                                    {{-- <div class="col-lg-11 col-8">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="q"
                                                value="{{ session()->has('q') ? session('q') : '' }}"
                                                placeholder="Ketik Nama Pengguna & Klik Enter ..."
                                                onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.form.submit(); }">
                                            <button class="btn btn-primary" type="submit" id="button-addon1"><i
                                                    class="fa-solid fa-magnifying-glass"></i></button>
                                        </div>
                                    </div> --}}
                                    <div class="col-lg-11 col-8">
                                        <select class="form-select" name="project_id" onchange="this.form.submit()">
                                            <option value="">Pilih Projek</option>
                                            @foreach ($projects as $comp)
                                                <option value="{{ $comp['id'] }}"
                                                    {{ session('project_id') == $comp['id'] ? 'selected' : '' }}>
                                                    {{ $comp['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-1 col-3">
                                        <a href="{{ route('progress.reset') }}" class="btn btn-secondary" type="button"
                                            id="button-addon2">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="row" id="kanban-container">
                            <div class="col-lg-4">
                                <div class="kanban-column border rounded p-2">
                                    <h5 class="text-center mt-1">Waiting</h5>
                                    <ul class="sortable-list list-group" id="waiting" data-status="WAITING">
                                        {{-- @foreach ($tasks->where('status', 'waiting') as $task) --}}
                                        @foreach ($activities['WAITING'] as $task)
                                            <li class="list-group-item kanban-item" data-id="{{ $task['id'] }}">
                                                {{ $task['title'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="kanban-column border rounded p-2">
                                    <h5 class="text-center mt-1">In Progress</h5>
                                    <ul class="sortable-list list-group" id="in_progress" data-status="ON PROGRESS">
                                        {{-- @foreach ($tasks->where('status', 'in_progress') as $task) --}}
                                        @foreach ($activities['ON PROGRESS'] as $task)
                                            <li class="list-group-item kanban-item" data-id="{{ $task['id'] }}">
                                                {{ $task['title'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="kanban-column border rounded p-2">
                                    <h5 class="text-center mt-1">Done</h5>
                                    <ul class="sortable-list list-group" id="done" data-status="DONE">
                                        {{-- @foreach ($tasks->where('status', 'done') as $task) --}}
                                        @foreach ($activities['DONE'] as $task)
                                            <li class="list-group-item kanban-item" data-id="{{ $task['id'] }}">
                                                {{ $task['title'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="table-responsive">
                            <table class="table table-striped" id="table">
                                <thead>
                                    <tr>
                                        <th width="30%">PL</th>
                                        <th>Paket</th>
                                        <th width="15%" class="text-center">Progress</th>
                                        @if (session('user.role') === 'SUPERADMIN' || session('user.role') === 'ADMIN' || session('user.project_leader'))
                                            <th width="10%" class="text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="table_body">
                                    @if (is_object($results) && method_exists($results, 'firstItem'))
                                        @foreach ($results as $project)
                                            @if (isset($project['progress']))
                                                @php
                                                    $status =
                                                        '<span class="badge ' .
                                                        ($project['progress'] ? 'bg-success' : 'bg-danger') .
                                                        '">' .
                                                        $project['progress'] .
                                                        '%</span>';
                                                @endphp
                                            @else
                                                @php
                                                    $status = '<span class="badge bg-danger">0%</span>';
                                                @endphp
                                            @endif
                                            <tr>
                                                <td>{{ $project['project_leader_name'] }}</td>
                                                <td>{{ $project['name'] }}</td>
                                                <td class="text-center">{!! $status !!}</td>
                                                @if (session('user.role') === 'SUPERADMIN' || session('user.role') === 'ADMIN' || session('user.project_leader'))
                                                    @if ($project['project_leader_id'] === session('user.id') || session('user.role') === 'SUPERADMIN' || session('user.role') === 'ADMIN')
                                                        <td class="text-center">
                                                            <a href="{{ route('progress.project', $project['id']) }}"
                                                                class="btn btn-sm btn-warning rounded-pill">
                                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                                            </a>
                                                        </td>
                                                    @else
                                                        <td class="text-center">
                                                            <span>-</span>
                                                        </td>
                                                    @endif
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        @if (is_object($results) && method_exists($results, 'onEachSide'))
                                            <td colspan="5"><span
                                                    style="margin-top: 15px;">{{ $results->appends(request()->query())->links() }}</span>
                                            </td>
                                        @endif
                                    </tr>
                                </tfoot>
                                </tbody>
                            </table>
                        </div> --}}
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

    <div id="modernImageModal" class="modern-modal" style="display: none" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modern-modal-content">
            <img id="modernImagePreview" alt="Preview">
        </div>
        <span class="closeImage" onclick="closeModernModal()">&times;</span>
    </div>

    <script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    @if (session()->has('success'))
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

    @if (session()->has('error'))
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
        document.addEventListener("DOMContentLoaded", function() {
            const statuses = [{
                    id: 'waiting',
                    status: 'WAITING'
                },
                {
                    id: 'in_progress',
                    status: 'ON PROGRESS'
                },
                {
                    id: 'done',
                    status: 'DONE'
                }
            ];

            console.log("Activities:", @json($activities['DONE']).length === 0);
            const projectId = "{{ session('project_id') }}";
            console.log("Project ID:", projectId === '');

            if (projectId === '') {
                $('#kanban-container').hide();
            } else {
                $('#kanban-container').show();
            }


            statuses.forEach(({
                id,
                status
            }) => {
                const el = document.getElementById(id);
                if (el) {
                    new Sortable(el, {
                        group: 'kanban',
                        animation: 150,
                        onEnd: function(evt) {
                            const itemId = evt.item.dataset.id;
                            const newStatus = evt.to.dataset.status;
                            changeStatus(itemId, newStatus);
                        }
                    });

                    el.dataset.status = status;
                } else {
                    console.warn(`Element #${id} not found`);
                }
            });
        });

        function changeStatus(id, status) {
            $('#fullPageLoader').show();
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
                success: function(response) {
                    // console.log(response);
                    if (response.status == 200) {
                        $('#fullPageLoader').hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // window.location.reload();
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

        function confirmDelete(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#fullPageLoader').show();
                    window.location.href = url;
                }
            });
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
