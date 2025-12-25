<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>eSchool - Login</title>
    <meta name="description"
        content="Login to eSchool Management System. Secure access for students, teachers, and guardians.">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.jsdelivr.net/npm; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; connect-src 'self';">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .login-card:hover {
            transform: translateY(-2px);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-1px);
        }

        .alert {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <!-- Logo/Title -->
                        <div class="text-center mb-4">
                            <i class="bi bi-mortarboard-fill text-primary" style="font-size: 3rem;"></i>
                            <h2 class="mt-3 mb-1">eSchool</h2>
                            <p class="text-muted">Management System</p>
                        </div>

                        <!-- Login Form -->
                        <form id="loginForm" autocomplete="on">

                            <!-- Error Messages -->
                            <div id="loginError" class="alert alert-danger d-none" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <span id="errorMessage"></span>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" required
                                        autofocus placeholder="Enter your email">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" required
                                        placeholder="Enter your password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="passwordToggleIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="loginButton">
                                    <i class="bi bi-box-arrow-in-right me-2" id="loginIcon"></i>
                                    <span id="loginText">Sign In</span>
                                </button>
                            </div>
                        </form>

                        <!-- Additional Links -->
                        <div class="text-center mt-4">
                            <p class="mb-0">
                                <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot your
                                    password?</a>
                            </p>
                            <hr class="my-3">
                            <p class="mb-0 text-muted">
                                Don't have an account?
                                <a href="{{ route('school.register') }}" class="text-decoration-none">Register
                                    School</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-white-50 small">
                        &copy; <span id="copyrightYear"></span> eSchool Management System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.5.0/dist/axios.min.js"></script>

    <!-- App Logic -->
    <script>
        class LoginManager {
            constructor() {
                this.form = document.getElementById('loginForm');
                this.emailInput = document.getElementById('email');
                this.passwordInput = document.getElementById('password');
                this.rememberInput = document.getElementById('remember');
                this.loginButton = document.getElementById('loginButton');
                this.loginIcon = document.getElementById('loginIcon');
                this.loginText = document.getElementById('loginText');
                this.errorDiv = document.getElementById('loginError');
                this.errorMessage = document.getElementById('errorMessage');

                this.initializeEventListeners();
                this.initializePasswordToggle();
            }

            initializeEventListeners() {
                // Form submission
                this.form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleLogin();
                });

                // Clear errors on input
                this.emailInput.addEventListener('input', () => this.clearFieldError('email'));
                this.passwordInput.addEventListener('input', () => this.clearFieldError('password'));

                // Enter key handling
                this.passwordInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.handleLogin();
                    }
                });
            }

            initializePasswordToggle() {
                const toggleButton = document.getElementById('togglePassword');
                const toggleIcon = document.getElementById('passwordToggleIcon');

                toggleButton.addEventListener('click', () => {
                    const type = this.passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    this.passwordInput.setAttribute('type', type);

                    // Toggle icon
                    if (type === 'text') {
                        toggleIcon.classList.remove('bi-eye');
                        toggleIcon.classList.add('bi-eye-slash');
                    } else {
                        toggleIcon.classList.remove('bi-eye-slash');
                        toggleIcon.classList.add('bi-eye');
                    }
                });
            }

            async handleLogin() {
                // Clear previous errors
                this.hideError();
                this.clearAllFieldErrors();

                // Basic validation
                const email = this.emailInput.value.trim();
                const password = this.passwordInput.value;

                if (!email) {
                    this.showFieldError('email', 'Email is required');
                    return;
                }

                if (!password) {
                    this.showFieldError('password', 'Password is required');
                    return;
                }

                if (!this.isValidEmail(email)) {
                    this.showFieldError('email', 'Please enter a valid email address');
                    return;
                }

                // Show loading state
                this.setLoadingState(true);

                try {
                    const remember = this.rememberInput.checked;

                    // Call the web login route
                    const response = await axios.post('/login', {
                        email,
                        password,
                        remember
                    });

                    // Extract user and token
                    const data = response.data.data || response.data;
                    const user = data.user;
                    const token = data.token;

                    // IMPORTANT: Save token for API requests
                    if (token) {
                        localStorage.setItem('auth_token', token);
                    }

                    // Show success message and redirect
                    await this.showSuccessMessage(user?.name || 'User');
                    this.redirectToDashboard();
                } catch (err) {
                    this.handleLoginError(err);
                } finally {
                    this.setLoadingState(false);
                }
            }

            handleLoginError(error) {
                console.error('Login error:', error);

                if (error.response) {
                    const {
                        status,
                        data
                    } = error.response;

                    if (status === 422 && data.errors) {
                        // Validation errors
                        Object.keys(data.errors).forEach(field => {
                            this.showFieldError(field, data.errors[field][0]);
                        });
                    } else if (status === 401) {
                        this.showError('Invalid email or password. Please try again.');
                    } else if (status === 429) {
                        this.showError('Too many login attempts. Please try again later.');
                    } else {
                        this.showError(data.message || 'Login failed. Please try again.');
                    }
                } else if (error.request) {
                    this.showError('Connection error. Please check your internet connection and try again.');
                } else {
                    this.showError(error.message || 'An unexpected error occurred. Please try again.');
                }
            }

            async showSuccessMessage(userName) {
                return new Promise((resolve) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome back!',
                        text: `Hello ${userName}, you've been logged in successfully.`,
                        timer: 2000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    }).then(() => {
                        resolve();
                    });
                });
            }

            setLoadingState(loading) {
                if (loading) {
                    this.loginButton.disabled = true;
                    this.loginIcon.classList.remove('bi-box-arrow-in-right');
                    this.loginIcon.classList.add('bi-arrow-clockwise');
                    this.loginIcon.style.animation = 'spin 1s linear infinite';
                    this.loginText.textContent = 'Signing In...';
                } else {
                    this.loginButton.disabled = false;
                    this.loginIcon.classList.remove('bi-arrow-clockwise');
                    this.loginIcon.classList.add('bi-box-arrow-in-right');
                    this.loginIcon.style.animation = '';
                    this.loginText.textContent = 'Sign In';
                }
            }

            showError(message) {
                this.errorMessage.textContent = message;
                this.errorDiv.classList.remove('d-none');
                setTimeout(() => this.hideError(), 10000);
            }

            hideError() {
                this.errorDiv.classList.add('d-none');
            }

            showFieldError(field, message) {
                const input = document.getElementById(field);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = input.parentNode.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = message;
                        feedback.style.display = 'block';
                    }
                }
            }

            clearFieldError(field) {
                const input = document.getElementById(field);
                if (input) {
                    input.classList.remove('is-invalid');
                    const feedback = input.parentNode.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'none';
                    }
                }
            }

            clearAllFieldErrors() {
                ['email', 'password'].forEach(field => this.clearFieldError(field));
            }

            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            redirectToDashboard() {
                window.location.href = '/dashboard';
            }
        }

        const initApp = () => {
            // 1. Copyright Year
            const yEl = document.getElementById('copyrightYear');
            if (yEl) yEl.textContent = String(new Date().getFullYear());

            // 2. Setup Axios
            if (window.axios) {
                window.axios.defaults.withCredentials = true;
                const token = document.querySelector('meta[name="csrf-token"]');
                if (token) {
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
                }

                // Global interceptor for 401/419
                window.axios.interceptors.response.use(
                    response => response,
                    error => {
                        const status = error.response ? error.response.status : null;
                        if (status === 401 || status === 419) {
                            console.warn('Session expired or unauthorized. Clearing auth.');
                            localStorage.removeItem('auth_token');
                            sessionStorage.removeItem('auth_token');

                            // Prevent redirect loops on login page
                            if (window.location.pathname !== '/' && window.location.pathname !== '/login') {
                                window.location.href = '/';
                            }
                        }
                        return Promise.reject(error);
                    }
                );
            }

            // 3. Initialize Login Manager
            new LoginManager();
        };

        document.addEventListener('DOMContentLoaded', initApp);
    </script>
</body>

</html>
