<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>eSchool - Login</title>
    <meta name="description" content="Login to eSchool Management System. Secure access for students, teachers, and guardians.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Tailwind & Alpine via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc; /* slate-50 */
        }
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(245, 158, 11, 0.15) 0px, transparent 50%);
            z-index: -2;
        }

        body::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.025;
            pointer-events: none;
            z-index: -1;
        }

        .animate-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden text-slate-900">
    <div x-data="loginForm()" class="w-full max-w-[440px] bg-white/98 border border-white/10 shadow-2xl shadow-black/10 rounded-3xl overflow-hidden animate-in">
        
        <!-- Header Gradient -->
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 px-8 py-10  text-center  text-white">
            <i class="bi bi-mortarboard-fill text-5xl  mb-6  inline-block drop-shadow-md"></i>
            <h2 class="text-3xl font-bold mb-2 tracking-tight">eSchool</h2>
            <p class="text-white/80 font-medium">Smart Education Management</p>
        </div>

        <!-- Body -->
        <div class="p-8">
            <form @submit.prevent="handleLogin" class="space-y-6">
                <!-- Error Alert -->
                <div x-show="errorMessage" x-transition.opacity class="bg-red-50 text-red-700 p-4 rounded-lg flex items-start gap-3 border border-red-100" style="display: none;">
                    <i class="bi bi-exclamation-circle-fill mt-0.5"></i>
                    <span x-text="errorMessage" class="text-sm font-medium"></span>
                </div>

                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                    <div class="relative group" :class="{ 'ring-2 ring-red-500 rounded-lg': fieldErrors.email }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-envelope text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input 
                            type="email" 
                            x-model="email" 
                            @input="fieldErrors.email = ''"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all text-slate-900" 
                            required 
                            autofocus
                            placeholder="name@school.com"
                            autocomplete="username"
                        >
                    </div>
                    <p x-show="fieldErrors.email" x-text="fieldErrors.email" class="mt-1 text-sm text-red-600 font-medium" style="display: none;"></p>
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-slate-700">Password</label>
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-700 hover:underline font-medium transition-colors">Forgot password?</a>
                    </div>
                    <div class="relative group" :class="{ 'ring-2 ring-red-500 rounded-lg': fieldErrors.password }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            x-model="password" 
                            @input="fieldErrors.password = ''"
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all text-slate-900" 
                            required
                            placeholder="Enter password"
                            autocomplete="current-password"
                        >
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" :aria-label="showPassword ? 'Hide password' : 'Show password'">
                            <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    <p x-show="fieldErrors.password" x-text="fieldErrors.password" class="mt-1 text-sm text-red-600 font-medium" style="display: none;"></p>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" x-model="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                    <label for="remember" class="ml-2 text-sm font-medium text-slate-600 cursor-pointer">Keep me signed in</label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    :disabled="loading"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm shadow-blue-500/30 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Signing in...' : 'Sign In'">Sign In</span>
                </button>
            </form>

            <div class="mt-8  text-center  border-t border-slate-100 pt-6">
                <p class="text-slate-500 text-sm">New here? <a href="{{ route('school.register') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">Create an account</a></p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.5.0/dist/axios.min.js"></script>

    <script>
        // Setup axios CSRF
        if (window.axios) {
            window.axios.defaults.withCredentials = true;
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('loginForm', () => ({
                email: '',
                password: '',
                remember: false,
                showPassword: false,
                loading: false,
                errorMessage: '',
                fieldErrors: {
                    email: '',
                    password: ''
                },
                
                async handleLogin() {
                    this.errorMessage = '';
                    this.fieldErrors = { email: '', password: '' };
                    
                    if (!this.email) this.fieldErrors.email = 'Email is required';
                    if (!this.password) this.fieldErrors.password = 'Password is required';
                    if (this.fieldErrors.email || this.fieldErrors.password) return;

                    this.loading = true;

                    try {
                        const response = await axios.post('/login', {
                            email: this.email,
                            password: this.password,
                            remember: this.remember
                        });

                        const data = response.data.data || response.data;
                        if (data.token) localStorage.setItem('auth_token', data.token);

                        Swal.fire({
                            icon: 'success',
                            title: 'Welcome Back!',
                            text: 'Redirecting to your dashboard...',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });

                        setTimeout(() => window.location.href = '/dashboard', 1000);
                    } catch (err) {
                        if (err.response?.status === 422) {
                            const errors = err.response.data.errors;
                            Object.keys(errors).forEach(field => {
                                this.fieldErrors[field] = errors[field][0];
                            });
                        } else {
                            this.errorMessage = err.response?.data?.message || 'Login failed. Please check your credentials.';
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>
