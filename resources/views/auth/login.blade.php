<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>eSchool - Login</title>
    <meta name="description"
        content="Login to eSchool Management System. Secure access for students, teachers, and guardians.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        body {
            background-color: var(--slate-900);
            min-height: 100vh;
            color: var(--slate-50);
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.08) 0px, transparent 50%);
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
            /* Subdued noise */
            pointer-events: none;
            z-index: -1;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border-radius: 1.5rem;
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .card-header-gradient {
            background: linear-gradient(135deg, var(--primary), #6366f1);
            padding: 3rem 2rem;
            text-align: center;
            color: white;
        }

        .login-logo-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            display: inline-block;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .card-body {
            padding: 2.5rem 2rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--slate-600);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .input-group {
            border-radius: 0.75rem;
            overflow: hidden;
            border: 2px solid var(--slate-200);
            transition: all 0.2s ease;
        }

        .input-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .input-group-text {
            background: var(--slate-50);
            border: none;
            color: var(--slate-400);
            padding-left: 1rem;
        }

        .form-control {
            border: none !important;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            background: white !important;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 0.75rem;
            padding: 0.8rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s ease;
            margin-top: 1rem;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .footer-links {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="login-card animate-in">
        <div class="card-header-gradient">
            <i class="bi bi-mortarboard-fill login-logo-icon"></i>
            <h2 class="mb-0 fw-bold">eSchool</h2>
            <p class="mb-0 opacity-75">Smart Education Management</p>
        </div>

        <div class="card-body">
            <form id="loginForm">
                <div id="loginError" class="alert alert-danger d-none" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <span id="errorMessage"></span>
                </div>

                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" required autofocus
                            placeholder="name@school.com">
                    </div>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">Password</label>
                        <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required
                            placeholder="Enter password">
                        <button class="btn btn-link text-slate-400 pe-3 text-decoration-none" type="button"
                            id="togglePassword">
                            <i class="bi bi-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label text-muted small" for="remember">Keep me signed in</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" id="loginButton">
                        <span id="loginText">Sign In</span>
                    </button>
                </div>
            </form>

            <div class="footer-links">
                <p class="text-muted mb-0">New here? <a href="{{ route('school.register') }}">Register your school</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.5.0/dist/axios.min.js"></script>

    <script>
        class LoginManager {
            constructor() {
                this.form = document.getElementById('loginForm');
                this.emailInput = document.getElementById('email');
                this.passwordInput = document.getElementById('password');
                this.rememberInput = document.getElementById('remember');
                this.loginButton = document.getElementById('loginButton');
                this.loginText = document.getElementById('loginText');
                this.errorDiv = document.getElementById('loginError');
                this.errorMessage = document.getElementById('errorMessage');

                this.initializeEventListeners();
                this.initializePasswordToggle();
            }

            initializeEventListeners() {
                this.form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleLogin();
                });
                this.emailInput.addEventListener('input', () => this.clearFieldError('email'));
                this.passwordInput.addEventListener('input', () => this.clearFieldError('password'));
            }

            initializePasswordToggle() {
                const toggleButton = document.getElementById('togglePassword');
                const toggleIcon = document.getElementById('passwordToggleIcon');
                toggleButton.addEventListener('click', () => {
                    const isPassword = this.passwordInput.type === 'password';
                    this.passwordInput.type = isPassword ? 'text' : 'password';
                    toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            async handleLogin() {
                this.hideError();
                this.clearAllFieldErrors();

                const email = this.emailInput.value.trim();
                const password = this.passwordInput.value;

                if (!email || !password) {
                    if (!email) this.showFieldError('email', 'Email is required');
                    if (!password) this.showFieldError('password', 'Password is required');
                    return;
                }

                this.setLoadingState(true);

                try {
                    const response = await axios.post('/login', {
                        email,
                        password,
                        remember: this.rememberInput.checked
                    });

                    const data = response.data.data || response.data;
                    if (data.token) localStorage.setItem('auth_token', data.token);

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Redirecting to your dashboard...',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });

                    setTimeout(() => window.location.href = '/dashboard', 1000);
                } catch (err) {
                    this.handleLoginError(err);
                } finally {
                    this.setLoadingState(false);
                }
            }

            handleLoginError(error) {
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach(field => {
                        this.showFieldError(field, errors[field][0]);
                    });
                } else {
                    this.showError(error.response?.data?.message || 'Login failed. Please check your credentials.');
                }
            }

            setLoadingState(loading) {
                this.loginButton.disabled = loading;
                this.loginText.textContent = loading ? 'Checking...' : 'Sign In';
            }

            showError(message) {
                this.errorMessage.textContent = message;
                this.errorDiv.classList.remove('d-none');
            }

            hideError() {
                this.errorDiv.classList.add('d-none');
            }

            showFieldError(field, message) {
                const input = document.getElementById(field);
                if (input) {
                    const group = input.closest('.input-group');
                    group.classList.add('border-danger');
                    const feedback = group.nextElementSibling;
                    if (feedback) {
                        feedback.textContent = message;
                        feedback.style.display = 'block';
                    }
                }
            }

            clearFieldError(field) {
                const input = document.getElementById(field);
                if (input) {
                    const group = input.closest('.input-group');
                    group.classList.remove('border-danger');
                    const feedback = group.nextElementSibling;
                    if (feedback) feedback.style.display = 'none';
                }
            }

            clearAllFieldErrors() {
                ['email', 'password'].forEach(f => this.clearFieldError(f));
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.axios) {
                window.axios.defaults.withCredentials = true;
                const token = document.querySelector('meta[name="csrf-token"]');
                if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
            }
            new LoginManager();
        });
    </script>
</body>

</html>
