<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'eSchool Management System — manage students, teachers, attendance and more.')">  
    
    @if(!config('app.debug'))
    <meta http-equiv="Content-Security-Policy"
        content="
      default-src 'self';
      script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net;
      style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net;
      img-src 'self' data: https://ui-avatars.com;
      font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net;
      connect-src 'self' ws: wss: https://cdn.jsdelivr.net;
      ">
    @endif

    <title>{{ config('app.name', 'eSchool') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind / Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    @include('layouts.partials.config')
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Sidebar Overlay -->
        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden transition-opacity"
            x-transition.opacity
            style="display: none;"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-sm transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col"
        >
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <h5 class="text-xl font-bold text-indigo-600">{{ config('app.name', 'eSchool') }}</h5>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto px-4 py-6 flex flex-col gap-1" id="sidebar-root">
                @php
                    $user = auth()->user();
                    $roles = $user ? $user->roles->pluck('name')->map(fn($r) => strtolower(str_replace(' ', '_', $r)))->toArray() : [];
                    $isSuperAdmin = in_array('super_admin', $roles);
                    $isSchoolAdmin = in_array('School Admin', $roles) || in_array('admin', $roles);
                @endphp
                
                @php
                    $isActive = function($path) {
                        return request()->is(trim($path, '/').'*');
                    };
                @endphp

                <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors mb-1 {{ request()->is('dashboard') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                    <i class="bi bi-grid text-lg"></i>
                    <span>Dashboard</span>
                </a>
                
                @if($isSuperAdmin)
                <div class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Super Admin</div>
                <a href="/super-admin/schools" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors mb-1 {{ $isActive('super-admin/schools') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                    <i class="bi bi-building text-lg"></i>
                    <span>Schools</span>
                </a>
                <a href="/super-admin/users" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors mb-1 {{ $isActive('super-admin/users') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                    <i class="bi bi-people text-lg"></i>
                    <span>System Users</span>
                </a>
                <a href="/roles" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors mb-1 {{ $isActive('roles') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                    <i class="bi bi-shield-lock text-lg"></i>
                    <span>Role Management</span>
                </a>
                @endif

                @if($isSuperAdmin || $isSchoolAdmin)
                <div class="px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Administration</div>
                
                <div x-data="{ open: {{ $isActive('academic') || $isActive('sessions') || $isActive('terms') || $isActive('classes') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-colors w-full {{ $isActive('academic') || $isActive('sessions') || $isActive('terms') || $isActive('classes') ? 'bg-slate-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-mortarboard text-lg"></i>
                            <span>Academic</span>
                        </div>
                        <i class="bi bi-chevron-down text-sm transition-transform duration-300" :class="{'rotate-180': open}"></i>
                    </button>
                    <div x-show="open" class="pl-9 pr-2 py-1 flex flex-col gap-1">
                        <a href="/sessions" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ $isActive('sessions') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-calendar-range text-sm"></i> <span>Sessions</span>
                        </a>
                        <a href="/terms" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ $isActive('terms') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-calendar3 text-sm"></i> <span>Terms</span>
                        </a>
                        <a href="/classes" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ $isActive('classes') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-grid text-sm"></i> <span>Classes</span>
                        </a>
                    </div>
                </div>

                <a href="/teachers" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors mb-1 {{ $isActive('teachers') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                    <i class="bi bi-person-badge text-lg"></i>
                    <span>Teachers</span>
                </a>
                
                <a href="/staff" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors mb-1 {{ $isActive('staff') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                    <i class="bi bi-person-gear text-lg"></i>
                    <span>Staff Management</span>
                </a>

                <div x-data="{ open: {{ $isActive('students') || $isActive('guardians') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-colors w-full {{ $isActive('students') || $isActive('guardians') ? 'bg-slate-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-people text-lg"></i>
                            <span>Students</span>
                        </div>
                        <i class="bi bi-chevron-down text-sm transition-transform duration-300" :class="{'rotate-180': open}"></i>
                    </button>
                    <div x-show="open" class="pl-9 pr-2 py-1 flex flex-col gap-1">
                        <a href="/students" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ request()->is('students*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-person text-sm"></i> <span>Students</span>
                        </a>
                        <a href="/guardians" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ request()->is('guardians*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-person-heart text-sm"></i> <span>Guardians</span>
                        </a>
                    </div>
                </div>
                
                <div x-data="{ open: {{ $isActive('assessment') || $isActive('assignments') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-colors w-full {{ $isActive('assessment') || $isActive('assignments') ? 'bg-slate-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-journal-check text-lg"></i>
                            <span>Assessment</span>
                        </div>
                        <i class="bi bi-chevron-down text-sm transition-transform duration-300" :class="{'rotate-180': open}"></i>
                    </button>
                    <div x-show="open" x-collapse class="pl-9 pr-2 py-1 flex flex-col gap-1">
                        <a href="/assignments" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ request()->is('assignments*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-journal-text text-sm"></i> <span>Assignments</span>
                        </a>
                        <a href="/assessments" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ request()->is('assessments*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-pencil-square text-sm"></i> <span>Assessments</span>
                        </a>
                    </div>
                </div>
                
                <div x-data="{ open: {{ $isActive('finance') || $isActive('fees') || $isActive('payments') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-colors w-full {{ $isActive('finance') || $isActive('fees') || $isActive('payments') ? 'bg-slate-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-cash-coin text-lg"></i>
                            <span>Finance</span>
                        </div>
                        <i class="bi bi-chevron-down text-sm transition-transform duration-300" :class="{'rotate-180': open}"></i>
                    </button>
                    <div x-show="open" x-collapse class="pl-9 pr-2 py-1 flex flex-col gap-1">
                        <a href="/fees" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ request()->is('fees*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-list-check text-sm"></i> <span>Fees List</span>
                        </a>
                        <a href="/payments" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ request()->is('payments*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-credit-card text-sm"></i> <span>Payments</span>
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ $isActive('settings') || $isActive('profile') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-colors w-full {{ $isActive('settings') || $isActive('profile') ? 'bg-slate-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-gear text-lg"></i>
                            <span>Settings</span>
                        </div>
                        <i class="bi bi-chevron-down text-sm transition-transform duration-300" :class="{'rotate-180': open}"></i>
                    </button>
                    <div x-show="open" x-collapse class="pl-9 pr-2 py-1 flex flex-col gap-1">
                        <a href="/profile" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm {{ request()->is('profile*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-building text-sm"></i> <span>School Profile</span>
                        </a>
                    </div>
                </div>
                @endif
                
                <a href="#" onclick="App.logout(event)" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 font-medium hover:bg-red-50 transition-colors mt-8">
                    <i class="bi bi-box-arrow-left text-lg"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden relative">
            
            <!-- Top Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 z-30">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="p-2 text-gray-500 hover:text-gray-700 lg:hidden rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 m-0">@yield('header_title', 'Dashboard')</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <div id="guardian-student-selector-container"></div>
                    
                    <!-- Notification Bell (Simplified Alpine Dropdown) -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="p-2 text-gray-500 hover:bg-gray-100 rounded-full relative transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span id="notificationBadge" class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full hidden border-2 border-white"></span>
                        </button>

                        <div x-show="open" style="display: none;" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50 transform origin-top-right transition-all">
                            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <span class="font-semibold text-gray-700">Notifications</span>
                            </div>
                            <div id="notificationList" class="max-h-80 overflow-y-auto">
                                <div class="p-8  text-center  text-gray-400" id="emptyNotif">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    <p class="text-sm">No new notifications</p>
                                </div>
                            </div>
                            <a href="{{ url('/chats') }}" class="block w-full  text-center  py-2.5 bg-gray-50 text-sm text-indigo-600 hover:text-indigo-700 hover:bg-gray-100 transition-colors font-medium border-t">
                                View All Messages
                            </a>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 p-1 pr-2 hover:bg-gray-100 rounded-full transition-colors">
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                            <div class="hidden md:block text-left">
                                <div class="text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Admin User' }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst(auth()->user()->roles->first()->name ?? 'Administrator') }}</div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" style="display: none;" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                My Profile
                            </a>
                            <div class="h-px bg-gray-100 my-1"></div>
                            <a href="#" onclick="App.logout(event)" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto bg-gray-50 relative">
                
                <!-- Full Page Loader Overlay -->
                <div id="page-loader" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-40 transition-opacity duration-300" style="opacity: 0; pointer-events: none;">
                    <div class=" text-center  bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
                        <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-gray-700 font-medium text-sm">Loading...</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6 lg:p-8 w-full max-w-[1600px] mx-auto">
                    @yield('content')
                </div>
            </div>
            
        </main>
    </div>

    <x-toast />

    @yield('scripts')
    <!-- Required for legacy Bootstrap Modals, Action Buttons, and SweetAlert2 dialogs to function -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="{{ asset('js/premium-app.js') }}?v={{ time() + 1 }}"></script>

    @auth
        <!-- Reverb Config -->
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
            document.addEventListener('DOMContentLoaded', () => {
                // Page loader helper functions
                window.showPageLoader = () => {
                    const loader = document.getElementById('page-loader');
                    loader.style.opacity = '1';
                    loader.style.pointerEvents = 'auto';
                };
                window.hidePageLoader = () => {
                    const loader = document.getElementById('page-loader');
                    loader.style.opacity = '0';
                    loader.style.pointerEvents = 'none';
                };

                const userId = "{{ auth()->id() }}";
                
                // Notifications logic
                // We leave the old list insertion logic here for now but restyled for Tailwind.
                if (userId && window.Echo) {
                    window.Echo.private(`chat.${userId}`)
                        .listen('.MessageSent', (e) => {
                            const badge = document.getElementById('notificationBadge');
                            if(badge) badge.classList.remove('hidden');
                            
                            const notifList = document.getElementById('notificationList');
                            const emptyNotif = document.getElementById('emptyNotif');
                            if(notifList) {
                                if(emptyNotif) emptyNotif.style.display = 'none';
                                
                                const item = document.createElement('a');
                                item.href = `/chats?partner_id=${e.sender_id}`;
                                item.className = 'flex items-start gap-3 p-4 hover:bg-gray-50 border-b border-gray-100 transition-colors';
                                
                                item.innerHTML = `
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(e.sender?.name ?? '')}&background=random" class="w-10 h-10 rounded-full">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <span class="font-semibold text-sm text-gray-900">${escapeText(e.sender?.name)}</span>
                                            <span class="text-xs text-gray-500">Just now</span>
                                        </div>
                                        <p class="text-sm text-gray-600 line-clamp-1">${escapeText(e.message)}</p>
                                    </div>
                                `;
                                notifList.prepend(item);
                            }
                            
                            // Use our new Toast component
                            if (!window.location.pathname.includes('/chats') && typeof showToast === 'function') {
                                showToast(`New message from ${e.sender?.name}`, 'info');
                            }
                        });
                }
                
                function escapeText(val) {
                    return String(val ?? "").replace(/[&<>"']/g, function(m) {
                        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
                    });
                }
            });
        </script>
    @endauth
</body>
</html>
