<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <div class="row">
                        <div class="col-sm-4 col-4">
                            <a href="{{ route('home') }}"><img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" srcset="" style="width: "></a>
                        </div>
                        <div class="col-sm-8 col-8">
                            <h5 class="mt-1">Proyek<br> Manajemen</h5>
                        </div>
                    </div>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-item {{ $title == 'Home' ? 'active' : ''  }} ">
                    <a href="{{ route('home') }}" class='sidebar-link'>
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item {{ $title == 'Search' ? 'active' : ''  }} ">
                    <a href="{{ route('search.index') }}" class='sidebar-link'>
                        <i class="fa-solid fa-search"></i>
                        <span>Pencarian Dokumen</span>
                    </a>
                </li>

            @if(session('user.role') === 'SUPERADMIN' || session('user.role') === 'ADMIN' || session('user.project_leader'))
                <li class="sidebar-item {{ $title == 'categoryAct' || $title == 'categoryAdm' ? 'active' : ''  }} has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="fa-solid fa-list"></i>
                        <span>Kategori</span>
                    </a>
                    <ul class="submenu {{ $title == 'categoryAct' || $title == 'categoryAdm' ? 'active' : '' }}">
                        @if (session('user.role') === 'SUPERADMIN' || session('user.role') === 'ADMIN')
                            <li class="submenu-item {{ $title == 'categoryAdm' ? 'active' : ''  }}">
                                <a href="{{ route('categoryAdm.index') }}">Administrasi</a>
                            </li>
                        @endif
                        <li class="submenu-item {{ $title == 'categoryAct' ? 'active' : ''  }}">
                            <a href="{{ route('categoryAct.index') }}">Aktivitas</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(session('user.role') === 'SUPERADMIN' || session('user.role') === 'ADMIN')
                <li class="sidebar-item {{ $title == 'company' ? 'active' : ''  }} ">
                    <a href="{{ route('company.index') }}" class='sidebar-link'>
                        <i class="fa-solid fa-building"></i>
                        <span>Perusahaan</span>
                    </a>
                </li>
            @endif

                <li class="sidebar-item {{ $title == 'project' ? 'active' : ''  }} ">
                    <a href="{{ route('project.index') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Proyek</span>
                    </a>
                </li>

                <li class="sidebar-item {{ $title == 'activity' ? 'active' : ''  }} ">
                    <a href="{{ route('activity.index') }}" class='sidebar-link'>
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Aktivitas</span>
                    </a>
                </li>

            {{-- @if(session('user.role') === 'SUPERADMIN')
                @php
                    $lastRoute = session()->get('lastRoute');
                    $lastRoute = $lastRoute ? explode(',', $lastRoute) : [];
                    $lastRoute = $lastRoute[0] ? explode('.', $lastRoute[0]) : [];
                    // if($lastRoute[1] == "create") {
                    //     $url = url()->current();
                    //     $url = explode('/', $url);
                    //     // $lastRoute[1] = $url[3];
                    // }
                    // if($lastRoute[1] == "project") {
                    //     $title = 'caProject';
                    // } else {
                    //     $title = 'ca';
                    // }

                @endphp --}}
                {{-- {{ json_encode($lastRoute   ) }} --}}
                @if (session('user.role') === 'SUPERADMIN' || session('user.role') === 'ADMIN' || session('user.project_leader'))
                <li class="sidebar-item {{ $title == 'progress' ? 'active' : ''  }} ">
                    <a href="{{ route('progress.index') }}" class='sidebar-link'>
                        <i class="fa-solid fa-signal"></i>
                        <span>Progress</span>
                    </a>
                </li>
                @endif
                @if (session('user.role') === 'SUPERADMIN' || session('user.role') === 'ADMIN')
                <li class="sidebar-item {{ $title == 'ca' || $title == 'caProject' ? 'active' : ''  }} has-sub" style="display: none">
                    <a href="#" class='sidebar-link'>
                        <i class="fa-solid fa-wallet"></i>
                        <span>CA</span>
                    </a>
                    <ul class="submenu {{ $title == 'ca' || $title == 'caProject' ? 'active' : '' }}">
                        <li class="submenu-item {{ $title == 'ca' ? 'active' : ''  }}">
                            <a href="{{ route('ca.index') }}">Pengajuan</a>
                        </li>
                        <li class="submenu-item {{ $title == 'caProject' ? 'active' : ''  }}">
                            <a href="{{ route('ca.project') }}">Pemakaian</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item {{ $title == 'user' ? 'active' : ''  }} ">
                    <a href="{{ route('user.index') }}" class='sidebar-link'>
                        <i class="fa-solid fa-users"></i>
                        <span>Pengguna</span>
                    </a>
                </li>
                @endif
            {{-- @endif --}}

                {{-- <li class="sidebar-item">
                    <a class="sidebar-link" onclick="document.getElementById('logout-form').submit();" style="cursor: pointer;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li> --}}

            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
