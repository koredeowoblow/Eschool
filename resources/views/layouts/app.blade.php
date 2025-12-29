<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- CSP disabled for ngrok development - re-enable in production --}}
    {{-- <meta http-equiv="Content-Security-Policy"
        content="
      default-src 'self' *.ngrok-free.app *.ngrok.io;
      script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net *.ngrok-free.app *.ngrok.io;
      style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com *.ngrok-free.app *.ngrok.io;
      img-src 'self' data: https://ui-avatars.com *.ngrok-free.app *.ngrok.io;
      font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net *.ngrok-free.app *.ngrok.io;
      connect-src 'self' https://cdn.jsdelivr.net ws: wss: *.ngrok-free.app *.ngrok.io;
      "> --}}

    <title>{{ config('app.name', 'eSchool') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/custom.min.css">

    <!-- GLOBAL APP CONFIG (ROUTES ONLY, NO USER DATA) -->
    <!-- GLOBAL APP CONFIG (ROUTES) -->
    @include('layouts.partials.config')
</head>

<body class="bg-light">

    <div class="admin-wrapper d-flex vh-100">

        <!-- Sidebar: Offcanvas on mobile, static on desktop -->
        <div class="offcanvas-lg offcanvas-start bg-white border-end" tabindex="-1" id="sidebarMenu">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title fw-bold text-primary">{{ config('app.name', 'eSchool') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                    data-bs-target="#sidebarMenu"></button>
            </div>
            <div class="offcanvas-body p-0">
                <aside id="sidebar-root" class="admin-sidebar w-100">

                </aside>
            </div>
        </div>

        <!-- MAIN -->
        <main class="admin-main flex-fill d-flex flex-column">

            <!-- Top Bar -->
            <header class="admin-header glass-effect d-flex align-items-center justify-content-between px-4">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebarToggle" class="btn btn-link p-0 text-dark me-3 d-lg-none"
                        data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                        <i class="bi bi-list fs-2"></i>
                    </button>
                    <a href="{{ route('dashboard') }}"
                        class="d-none d-md-flex align-items-center gap-2 text-decoration-none">
                        <i class="bi bi-mortarboard-fill text-primary fs-3"></i>
                        <span class="fs-4 fw-bold text-primary">{{ config('app.name', 'eSchool') }}</span>
                    </a>
                    <div class="vr d-none d-md-block"></div>
                    <div>
                        <h5 class="m-0 fw-bold text-primary">@yield('header_title', 'Dashboard')</h5>
                        <small class="text-muted d-none d-sm-block">Manage your school activities at a glance</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div id="guardian-student-selector-container"></div>
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle p-2 position-relative shadow-sm" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
                            <i class="bi bi-bell"></i>
                            <span id="notificationBadge"
                                class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 py-0 overflow-hidden"
                            style="width: 300px; max-height: 400px; font-size: 0.85rem;"
                            aria-labelledby="notificationBell">
                            <div
                                class="p-2 border-bottom bg-light fw-bold d-flex justify-content-between align-items-center">
                                <span>Notifications</span>
                                <span class="badge bg-primary rounded-pill d-none" id="notifCount">0</span>
                            </div>
                            <div id="notificationList" class="overflow-auto" style="max-height: 340px;">
                                <div class="p-3 text-center text-muted" id="emptyNotif">
                                    <i class="bi bi-bell-slash d-block mb-1 opacity-50" style="font-size: 1.5rem;"></i>
                                    No new notifications
                                </div>
                            </div>
                            <a href="{{ url('/chats') }}"
                                class="dropdown-item text-center py-2 border-top bg-light small">
                                View All Messages
                            </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 text-decoration-none d-flex align-items-center gap-2"
                            type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end d-none d-md-block">
                                <small
                                    class="d-block fw-bold text-dark">{{ auth()->user()->name ?? 'Admin User' }}</small>
                                <small class="d-block text-muted" style="font-size: 0.75rem;">
                                    {{ ucfirst(auth()->user()->roles->first()->name ?? 'Administrator') }}
                                </small>
                            </div>
                            <img src="{{ auth()->user()->profile_photo_url }}"
                                alt="{{ auth()->user()->name ?? 'User' }}" class="rounded-circle" width="40"
                                height="40">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person-circle me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout', [], false) }}" method="POST" id="logoutForm">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"
                                        onclick="event.preventDefault(); App.logout();">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <section class="admin-content flex-fill bg-light-noise">
                <div class="container-fluid px-3 px-md-4 py-4">
                    @yield('content')
                </div>
            </section>

            <!-- Footer -->
            <footer class="text-center py-3 border-top text-muted bg-white">
                <small>&copy; {{ date('Y') }} {{ config('app.name') }}</small>
            </footer>

        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

    <!-- Real-time Messaging (Echo & Pusher CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js" defer></script>

    <script>
        window.Laravel = {
            reverb: {
                key: "{{ config('services.reverb.app_key') }}",
                host: "{{ config('services.reverb.host') }}",
                port: "{{ config('services.reverb.port') }}",
                scheme: "{{ config('services.reverb.scheme') }}"
            }
        };
    </script>

    <script>
        // document.addEventListener('DOMContentLoaded', () => {
        //     // Initialize Echo after scripts are loaded
        //     if (window.Pusher && window.Echo) {
        //         window.Pusher = Pusher;
        //         console.log('Echo Init:', {
        //             host: "{{ config('broadcasting.connections.reverb.options.host') }}",
        //             port: {{ config('broadcasting.connections.reverb.options.port', 8080) }},
        //             key: "{{ config('broadcasting.connections.reverb.key') }}"
        //         });
        //         window.Echo = new Echo({
        //             broadcaster: 'reverb',
        //             key: "{{ config('broadcasting.connections.reverb.key') }}",
        //             wsHost: "{{ config('broadcasting.connections.reverb.options.host') }}",
        //             wsPort: {{ config('broadcasting.connections.reverb.options.port') ?? 443 }},
        //             wssPort: {{ config('broadcasting.connections.reverb.options.port') ?? 443 }},
        //             forceTLS: {{ config('broadcasting.connections.reverb.options.useTLS') ? 'true' : 'false' }},
        //             enabledTransports: ['ws', 'wss'],
        //             disableStats: true,
        //         });
        //     }
        // });
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Pusher) return;

            // Use the public runtime config injected above
            let config = window.Laravel.reverb;

            // Clean up host if it includes protocol
            if (config.host) {
                config.host = config.host.replace(/^https?:\/\//, '').replace(/\/$/, '');
            }

            // Smart Auto-Configuration for Render/Production
            // If host is 0.0.0.0 (server binding), fallback to current hostname
            if (config.host === '0.0.0.0') {
                console.warn('Echo: Detected 0.0.0.0 host, falling back to window.location.hostname');
                config.host = window.location.hostname;
            }

            // Force WSS and Port 443 if on HTTPS
            if (window.location.protocol === 'https:') {
                config.scheme = 'https';
                config.port = 443;
            }

            console.log('Echo Init (Smart Config):', config);

            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: config.key,
                wsHost: config.host,
                wsPort: config.port,
                wssPort: config.port,
                forceTLS: config.scheme === 'https',
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
            });
        });
    </script>

    <!-- MAIN MODULE -->
    <script type="module" src="/js/premium-app.js" defer></script>

    @yield('scripts')

    <!-- Global Notification Listener -->
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const userId = "{{ auth()->id() }}";
                const notificationBadge = document.getElementById('notificationBadge');
                const notificationList = document.getElementById('notificationList');
                const emptyNotif = document.getElementById('emptyNotif');
                const notifBell = document.getElementById('notificationBell');

                // Clear badge when dropdown is opened
                if (notifBell) {
                    notifBell.addEventListener('show.bs.dropdown', () => {
                        if (notificationBadge) notificationBadge.classList.add('d-none');
                    });
                }

                // Handle clicking on notification items
                if (notificationList) {
                    notificationList.addEventListener('click', (e) => {
                        const item = e.target.closest('.dropdown-item');
                        if (item) {
                            item.remove();
                            if (notificationList.querySelectorAll('.dropdown-item').length === 0) {
                                if (emptyNotif) emptyNotif.classList.remove('d-none');
                            }
                        }
                    });
                }

                if (userId && window.Echo) {
                    // console.log('Global: Registering listener on chat.' + userId);
                    window.Echo.private(`chat.${userId}`)
                        .subscribed(() => {
                            // console.log('Global: Successfully subscribed to chat.' + userId);
                        })
                        .error((err) => {
                            // console.error('Global: Subscription error on chat.' + userId, err);
                        })
                        .listen('.MessageSent', (e) => {
                            // console.log('Global: Real-time message received:', e);

                            // Show badge
                            if (notificationBadge) {
                                notificationBadge.classList.remove('d-none');
                            }

                            // Add to notification list
                            if (notificationList) {
                                if (emptyNotif) emptyNotif.classList.add('d-none');

                                const item = document.createElement('a');
                                // Use partner_id to open the conversation
                                item.href = `/chats?partner_id=${e.sender_id}`;
                                item.className = 'dropdown-item p-3 border-bottom d-flex align-items-start gap-2';
                                item.style.whiteSpace = 'normal';

                                // Security: Sanitize all inputs before rendering
                                const safeName = document.createTextNode(e.sender.name).wholeText;
                                const safeMessage = document.createTextNode(e.message).wholeText;
                                const safeAvatar =
                                    `https://ui-avatars.com/api/?name=${encodeURIComponent(e.sender.name)}&background=random`;

                                item.innerHTML = `
                                    <img src="${safeAvatar}"
                                         class="rounded-circle" width="32" height="32" alt="">
                                    <div class="flex-fill">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark" id="notif-name-${e.id}"></strong>
                                            <small class="text-muted" style="font-size: 0.7rem;">Just now</small>
                                        </div>
                                        <div class="text-muted text-truncate" style="max-width: 180px;" id="notif-msg-${e.id}"></div>
                                    </div>
                                `;
                                notificationList.prepend(item);

                                // Set text content via ID to ensure secondary escaping
                                const nameEl = item.querySelector(`#notif-name-${e.id}`);
                                const msgEl = item.querySelector(`#notif-msg-${e.id}`);
                                if (nameEl) nameEl.textContent = e.sender.name;
                                if (msgEl) msgEl.textContent = e.message;
                            }

                            // Show toast if not on chat page
                            if (!window.location.pathname.includes('/chats')) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });

                                Toast.fire({
                                    icon: 'info',
                                    title: `New message from ${e.sender.name}`,
                                    text: e.message.substring(0, 50) + (e.message.length > 50 ? '...' : '')
                                });
                            }
                        });
                }
            });
        </script>
    @endauth
</body>

</html>
