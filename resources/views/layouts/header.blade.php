    <!--  BEGIN NAVBAR  -->
    <div class="header-container container-xxl">
        <header class="header navbar navbar-expand-sm expand-header">

            <a href="javascript:void(0);" class="sidebarCollapse">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </a>

            <div class="search-animated toggle-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <form class="form-inline search-full form-inline search" role="search" action="{{ route('search') }}" method="GET">
                    <div class="search-bar">
                        <input type="text" name="q" class="form-control search-form-control ml-lg-auto" placeholder="Cari surat (no. surat, agenda, perihal, instansi)..." value="{{ request('q') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x search-close"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </div>
                </form>
                <span class="badge badge-secondary">Ctrl + /</span>
            </div>

            <ul class="navbar-item flex-row ms-lg-auto ms-0">

                {{-- <li class="nav-item dropdown language-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="language-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ asset('templates/assets/img/1x1/us.svg') }}" class="flag-width" alt="flag">
                    </a>
                    <div class="dropdown-menu position-absolute" aria-labelledby="language-dropdown">
                        <a class="dropdown-item d-flex" href="javascript:void(0);"><img src="{{ asset('templates/assets/img/1x1/us.svg') }}" class="flag-width" alt="flag"> <span class="align-self-center">&nbsp;English</span></a>
                        <a class="dropdown-item d-flex" href="javascript:void(0);"><img src="{{ asset('templates/assets/img/1x1/tr.svg') }}" class="flag-width" alt="flag"> <span class="align-self-center">&nbsp;Turkish</span></a>
                        <a class="dropdown-item d-flex" href="javascript:void(0);"><img src="{{ asset('templates/assets/img/1x1/br.svg') }}" class="flag-width" alt="flag"> <span class="align-self-center">&nbsp;Portuguese</span></a>
                        <a class="dropdown-item d-flex" href="javascript:void(0);"><img src="{{ asset('templates/assets/img/1x1/in.svg') }}" class="flag-width" alt="flag"> <span class="align-self-center">&nbsp;Hindi</span></a>
                        <a class="dropdown-item d-flex" href="javascript:void(0);"><img src="{{ asset('templates/assets/img/1x1/de.svg') }}" class="flag-width" alt="flag"> <span class="align-self-center">&nbsp;German</span></a>
                    </div>
                </li> --}}

                <li class="nav-item theme-toggle-item">
                    <a href="javascript:void(0);" class="nav-link theme-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon dark-mode"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun light-mode"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    </a>
                </li>

                <li class="nav-item dropdown notification-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span class="badge badge-success {{ Auth::user()->unreadNotifications->count() > 0 ? '' : 'd-none' }}" id="notifBadge">{{ Auth::user()->unreadNotifications->count() }}</span>
                    </a>

                    <div class="dropdown-menu position-absolute" aria-labelledby="notificationDropdown" style="min-width: 340px;">
                        <div class="drodpown-title message p-2 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Notifikasi Persuratan</h6>
                            <button class="btn btn-sm btn-link text-decoration-none p-0" onclick="markAllNotificationsRead()" title="Tandai semua sudah dibaca">
                                <small>Tandai dibaca</small>
                            </button>
                        </div>
                        <div class="notification-scroll" id="notifListContainer" style="max-height: 350px; overflow-y: auto;">
                            @forelse(Auth::user()->notifications()->latest()->limit(7)->get() as $notif)
                                <div class="dropdown-item p-2 border-bottom {{ is_null($notif->read_at) ? 'bg-light-primary' : '' }}" id="notif-{{ $notif->id }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="p-1 rounded bg-primary text-white mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        </div>
                                        <div class="flex-grow-1" onclick="handleNotifClick('{{ $notif->id }}', '{{ $notif->data['surat_uuid'] ?? '' }}')" style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong class="small text-dark">{{ $notif->data['title'] ?? 'Notifikasi' }}</strong>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0 text-muted small text-truncate" style="max-width: 250px;">{{ $notif->data['perihal'] ?? ($notif->data['message'] ?? '-') }}</p>
                                            <small class="text-secondary" style="font-size: 0.7rem;">Dari: {{ $notif->data['pengirim_nama'] ?? '-' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted" id="notifEmptyState">
                                    <small>Tidak ada notifikasi baru.</small>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-container">
                            <div class="avatar avatar-sm avatar-indicators avatar-online">
                                <img alt="avatar" src="{{ asset('templates/assets/img/profile-30.png') }}" class="rounded-circle">
                            </div>
                        </div>
                    </a>

                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">
                                <div class="emoji me-2">
                                    &#x1F44B;
                                </div>
                                <div class="media-body">
                                    <h5>{{ Auth::user()->nama_lengkap }}</h5>
                                    <p>{{ Auth::user()->jurusan }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="dropdown-item">
                            <a href="user-profile.html">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> <span>Profile</span>
                            </a>
                        </div> --}}
                        {{-- <div class="dropdown-item">
                            <a href="app-mailbox.html">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg> <span>Inbox</span>
                            </a>
                        </div>
                        <div class="dropdown-item">
                            <a href="auth-boxed-lockscreen.html">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> <span>Lock Screen</span>
                            </a>
                        </div> --}}
                        <div class="dropdown-item">
                            <a href="javascript:void(0)" onclick="_logout()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> 
                                <span>Log Out</span>
                            </a>
                        </div>
                    </div>
                    
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <form action="{{ route('logout') }}" method="POST" id="flogout">@csrf</form>

    <script>
        function updateNotifBadge(count) {
            const badge = document.getElementById('notifBadge');
            if (!badge) return;
            if (count > 0) {
                badge.innerText = count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        function handleNotifClick(notifId, suratUuid) {
            $.ajax({
                url: `/notifikasi/${notifId}/mark-read`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    updateNotifBadge(res.unread_count);
                    const item = document.getElementById(`notif-${notifId}`);
                    if (item) item.classList.remove('bg-light-primary');
                    if (suratUuid) {
                        window.location.href = `{{ route('inbox') }}`;
                    }
                },
                error: function () {
                    if (suratUuid) window.location.href = `{{ route('inbox') }}`;
                }
            });
        }

        function markAllNotificationsRead() {
            $.ajax({
                url: `{{ route('notifikasi.mark_all_read') }}`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    updateNotifBadge(0);
                    $('.notification-dropdown .bg-light-primary').removeClass('bg-light-primary');
                    if (typeof Toast !== 'undefined') {
                        Toast.fire({ icon: 'success', title: 'Semua notifikasi ditandai dibaca.' });
                    }
                }
            });
        }

        function initEchoListener() {
            if (typeof window.Echo !== 'undefined') {
                window.Echo.private('App.Models.User.{{ Auth::id() }}')
                    .notification((notification) => {
                        const currentBadge = parseInt($('#notifBadge').text() || '0') + 1;
                        updateNotifBadge(currentBadge);

                        const html = `
                            <div class="dropdown-item p-2 border-bottom bg-light-primary" id="notif-${notification.id}">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="p-1 rounded bg-primary text-white mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    </div>
                                    <div class="flex-grow-1" onclick="handleNotifClick('${notification.id}', '${notification.surat_uuid}')" style="cursor: pointer;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="small text-dark">${notification.title || 'Notifikasi'}</strong>
                                            <small class="text-muted" style="font-size: 0.75rem;">Baru saja</small>
                                        </div>
                                        <p class="mb-0 text-muted small text-truncate" style="max-width: 250px;">${notification.perihal || notification.message}</p>
                                        <small class="text-secondary" style="font-size: 0.7rem;">Dari: ${notification.pengirim_nama}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#notifEmptyState').remove();
                        $('#notifListContainer').prepend(html);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'info',
                                title: notification.title,
                                text: notification.message,
                                showConfirmButton: false,
                                timer: 6000
                            });
                        }
                    });
            } else if (window.__echoRetries === undefined || window.__echoRetries < 25) {
                window.__echoRetries = (window.__echoRetries || 0) + 1;
                setTimeout(initEchoListener, 200);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initEchoListener();
        });
    </script>