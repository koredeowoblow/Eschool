<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>eSchool - Forgot Password</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        <style>body {
            background: var(--ui-bg, #f8fafc);
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
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(249, 115, 22, 0.08) 0px, transparent 50%);
            z-index: -1;
            pointer-events: none;
        }

        .login-card {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
        }

        .form-control:focus {
            border-color: var(--color-orange-400);
            box-shadow: 0 0 0 4px var(--ui-focus-ring);
        }

        .btn-primary {
            background: var(--color-primary-600);
            border: none;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: var(--color-primary-500);
            transform: translateY(-1px);
        }

        .text-primary {
            color: var(--color-primary-600) !important;
        }

        .link-support {
            color: var(--color-orange-600);
            text-decoration: none;
            font-weight: 500;
        }

        .link-support:hover {
            color: var(--color-orange-700);
            text-decoration: underline;
        }
    </style>
    </style>
</head>

<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-key-fill text-primary" style="font-size: 3rem;"></i>
                            <h2 class="mt-3 mb-1">Forgot Password?</h2>
                            <p class="text-muted">Enter your email to receive a reset link.</p>
                        </div>
                        <form id="forgotPasswordForm">
                            <div id="statusMessage" class="alert d-none" role="alert"></div>
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" required autofocus
                                        placeholder="Enter your email">
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <span id="btnText">Send Reset Link</span>
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
        document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const originalText = document.getElementById('btnText').textContent;
            const statusDiv = document.getElementById('statusMessage');

            btn.disabled = true;
            document.getElementById('btnText').textContent = 'Sending...';
            statusDiv.classList.add('d-none');

            try {
                const response = await axios.post('/api/v1/forgot-password', {
                    email: document.getElementById('email').value
                });
                statusDiv.className = 'alert alert-success';
                statusDiv.textContent = response.data.message || 'Reset link sent!';
                statusDiv.classList.remove('d-none');
            } catch (error) {
                statusDiv.className = 'alert alert-danger';
                statusDiv.textContent = error.response?.data?.message || 'Failed to send reset link.';
                statusDiv.classList.remove('d-none');
            } finally {
                btn.disabled = false;
                document.getElementById('btnText').textContent = originalText;
            }
        });
    </script>
</body>

</html>
