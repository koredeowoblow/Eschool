<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>eSchool - Reset Password</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
        }

        body {
            background: #f8fafc;
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
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
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.10) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(245, 158, 11, 0.10) 0px, transparent 50%);
            z-index: -1;
            pointer-events: none;
        }

        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .text-primary {
            color: var(--primary) !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                            <h2 class="mt-3 mb-1">Reset Password</h2>
                            <p class="text-muted">Enter your email and new password.</p>
                        </div>
                        <form id="resetPasswordForm">
                            <input type="hidden" id="token" value="{{ $token }}">
                            <div id="statusMessage" class="alert d-none" role="alert"></div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" required autofocus
                                        placeholder="Confirm your email">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" required
                                        placeholder="New password">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="password_confirmation" required
                                        placeholder="Confirm new password">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <span id="btnText">Reset Password</span>
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-light">Back to Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.5.0/dist/axios.min.js"></script>
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
        }

        document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const originalText = document.getElementById('btnText').textContent;
            const statusDiv = document.getElementById('statusMessage');

            // Validation
            const p1 = document.getElementById('password').value;
            const p2 = document.getElementById('password_confirmation').value;
            if (p1 !== p2) {
                statusDiv.className = 'alert alert-danger';
                statusDiv.textContent = 'Passwords do not match.';
                statusDiv.classList.remove('d-none');
                return;
            }

            btn.disabled = true;
            document.getElementById('btnText').textContent = 'Processing...';
            statusDiv.classList.add('d-none');

            try {
                const response = await axios.post('/api/v1/reset-password', {
                    token: document.getElementById('token').value,
                    email: document.getElementById('email').value,
                    password: p1,
                    password_confirmation: p2
                });
                statusDiv.className = 'alert alert-success';
                statusDiv.textContent = response.data.message || 'Password reset successfully!';
                statusDiv.classList.remove('d-none');

                setTimeout(() => window.location.href = '/login', 2000);
            } catch (error) {
                statusDiv.className = 'alert alert-danger';
                statusDiv.textContent = error.response?.data?.message || 'Failed to reset password.';
                statusDiv.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                document.getElementById('btnText').textContent = originalText;
            }
        });
    </script>
</body>

</html>
