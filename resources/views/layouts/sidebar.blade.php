    <div class="sidebar-wrapper sidebar-theme">

        <nav id="sidebar">

            <div class="navbar-nav theme-brand flex-row  text-center">
                <div class="nav-logo">
                    <div class="nav-item theme-logo">
                        <a href="/">
                            {{-- <img src="{{ asset('templates/assets/img/logo.svg') }}" class="navbar" alt="logo"> --}}
                            <img src="{{ asset('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" class="navbar" alt="logo" style="width: auto !important;">
                        </a>
                    </div>
                    <div class="nav-item theme-text">
                        <a href="/" class="nav-link"> SIPERMAS </a>
                    </div>
                </div>
                <div class="nav-item sidebar-toggle">
                    <div class="btn-toggle sidebarCollapse">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
                    </div>
                </div>
            </div>
            <div class="profile-info">
                <div class="user-info">
                    <div class="profile-img">
                        <img src="{{ asset('templates/assets/img/profile-30.png') }}" alt="avatar">
                    </div>
                    <div class="profile-content">
                        <h6 class="">{{ Auth::user()->nama_lengkap }}</h6>
                        <p class="">{{ Auth::user()->jurusan }}</p>
                        <p>{{ Auth::user()->leveluser->nama }}</p>
                    </div>
                </div>
            </div>
                            
            <div class="shadow-bottom"></div>
            <ul class="list-unstyled menu-categories" id="menusidebar">
                <li class="menu {{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span>Dashboard</span>
                        </div>
                    </a>
                </li>

                <li class="menu menu-heading">
                    <div class="heading">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>ARSIP</span>
                    </div>
                </li>

                @canany(['lihat surat masuk', 'input surat masuk', 'edit surat masuk', 'hapus surat masuk', 'cetak surat masuk'])
                <li class="menu {{ request()->routeIs(['inbox', 'inbox.show', 'inbox.create', 'inbox.edit']) ? 'active' : '' }}">
                    <a  href="#inbox" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                            <span>Surat Masuk</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled {{ request()->routeIs(['inbox', 'inbox.show', 'inbox.create', 'inbox.edit']) ? 'show' : '' }}" id="inbox" data-bs-parent="#menusidebar">
                        @canany(['input surat masuk', 'edit surat masuk'])
                        <li class="{{ request()->routeIs(['inbox.create']) ? 'active' : '' }}">
                            <a href="{{ route('inbox.create') }}"> Input Data </a>
                        </li>
                        @endcanany
                        @canany(['lihat surat masuk', 'edit surat masuk', 'hapus surat masuk', 'cetak surat masuk'])
                        <li class="{{ request()->routeIs(['inbox']) ? 'active' : '' }}">
                            <a href="{{ route('inbox') }}"> Daftar Surat </a>
                        </li>
                        @endcanany
                    </ul>
                </li>
                @endcanany

                @canany(['lihat surat keluar', 'input surat keluar', 'edit surat keluar', 'hapus surat keluar', 'cetak surat keluar', 'duplikat surat'])
                <li class="menu {{ request()->routeIs(['outbox', 'outbox.show', 'outbox.create', 'outbox.edit']) ? 'active' : '' }}">
                    <a href="#outbox" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <span>Surat Keluar</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled {{ request()->routeIs(['outbox', 'outbox.show', 'outbox.create', 'outbox.edit', 'outbox.duplicate']) ? 'show' : '' }}" id="outbox" data-bs-parent="#menusidebar">
                        @canany(['input surat keluar', 'edit surat keluar'])
                        <li class="{{ request()->routeIs(['outbox.create']) ? 'active' : '' }}">
                            <a href="{{ route('outbox.create') }}"> Input Data </a>
                        </li>
                        @endcanany
                        @canany(['lihat surat keluar', 'edit surat keluar', 'hapus surat keluar', 'cetak surat keluar'])
                        <li class="{{ request()->routeIs(['outbox']) ? 'active' : '' }}">
                            <a href="{{ route('outbox') }}"> Daftar Surat </a>
                        </li>
                        @endcanany
                        @canany(['duplikat surat'])
                        <li class="{{ request()->routeIs(['outbox.duplicate']) ? 'active' : '' }}">
                            <a href="{{ route('outbox.duplicate') }}"> Duplikat Surat </a>
                        </li>
                        @endcanany
                    </ul>
                </li>
                @endcanany
                
                {{-- <li class="menu">
                    <a href="./app-calendar.html" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            <span>Disposisi</span>
                        </div>
                    </a>
                </li>
                <li class="menu">
                    <a href="./app-calendar.html" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            <span>Tindak Lanjut</span>
                        </div>
                    </a>
                </li> --}}

                @canany(['spd'])
                <li class="menu {{ request()->routeIs(['sppd']) ? 'active' : '' }}">
                    <a href="{{ route('sppd') }}" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            <span>Data SPD</span>
                        </div>
                    </a>
                </li>
                @endcanany

                @canany(['tindak lanjut', 'agenda', 'input agenda', 'edit agenda', 'hapus agenda', 'statistik'])
                <li class="menu {{ request()->routeIs(['report.statistik']) ? 'active' : '' }}">
                    <a  href="#report" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
                            <span>Laporan</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled {{ request()->routeIs(['report.statistik', 'report.next', 'report.agenda']) ? 'show' : '' }}" id="report" data-bs-parent="#menusidebar">
                        {{-- <li class="{{ request()->routeIs(['inbox.create']) ? 'active' : '' }}">
                            <a href="{{ route('inbox.create') }}"> Disposisi </a>
                        </li> --}}
                        @can('tindak lanjut')
                        <li class="{{ request()->routeIs(['report.next']) ? 'active' : '' }}">
                            <a href="{{ route('report.next') }}"> Tindak Lanjut </a>
                        </li>
                        @endcan
                        @canany(['agenda', 'input agenda', 'edit agenda'])
                        <li class="{{ request()->routeIs(['report.agenda']) ? 'active' : '' }}">
                            <a href="{{ route('report.agenda') }}"> Agenda </a>
                        </li>
                        @endcanany
                        @can('statistik')
                        <li class="{{ request()->routeIs(['report.statistik']) ? 'active' : '' }}">
                            <a href="{{ route('report.statistik') }}"> Statistik </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                @role(['administrator', 'admin'])
                <li class="menu menu-heading">
                    <div class="heading">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>KONFIGURASI SISTEM</span>
                    </div>
                </li>

                {{-- <li class="menu">
                    <a href="/" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Profile</span>
                        </div>
                    </a>
                </li> --}}
                <li class="menu {{ request()->routeIs(['instansi']) ? 'active' : '' }}">
                    <a href="{{ route('instansi') }}" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> --}}
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"></path></svg>
                            <span>Daftar Instansi</span>
                        </div>
                    </a>
                </li>
                {{-- <li class="menu">
                    <a href="./app-chat.html" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            <span>Reset Nomor</span>
                        </div>
                    </a>
                </li> --}}
                <li class="menu {{ request()->routeIs(['user']) ? 'active' : '' }}">
                    <a href="{{ route('user') }}" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Daftar User</span>
                        </div>
                    </a>
                </li>
                <li class="menu {{ request()->routeIs(['pimpinan']) ? 'active' : '' }}">
                    <a href="{{ route('pimpinan') }}" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Data Pimpinan</span>
                        </div>
                    </a>
                </li>
                <li class="menu {{ request()->routeIs(['apps']) ? 'active' : '' }}">
                    <a href="{{ route('apps') }}" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> --}}
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                            <span>Aplikasi</span>
                        </div>
                    </a>
                </li>
                {{-- <li class="menu {{ request()->routeIs(['outbox', 'outbox.show', 'outbox.create', 'outbox.edit']) ? 'active' : '' }}">
                    <a href="#setting" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span>Pengaturan</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled {{ request()->routeIs(['outbox', 'outbox.show', 'outbox.create', 'outbox.edit']) ? 'show' : '' }}" id="setting" data-bs-parent="#menusidebar">
                        <li class="{{ request()->routeIs(['outbox.create']) ? 'active' : '' }}">
                            <a href="{{ route('outbox.create') }}"> Aplikasi </a>
                        </li>
                        <li class="{{ request()->routeIs(['outbox']) ? 'active' : '' }}">
                            <a href="{{ route('outbox') }}"> Daftar User </a>
                        </li>
                    </ul>
                </li> --}}
                @endrole
               
            </ul>
            
        </nav>

    </div>