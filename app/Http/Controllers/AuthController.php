<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\School\CreateRequest;
use App\Services\AuthService;
use App\Services\SuperAdmin\SchoolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\ResponseHelper;

class AuthController extends Controller
{
    protected $authService;
    protected $schoolService;

    public function __construct(AuthService $authService, SchoolService $schoolService)
    {
        $this->authService = $authService;
        $this->schoolService = $schoolService;
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Show the forgot password form.
     */
    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Show the reset password form.
     */
    public function resetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Get the authenticated User.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return ResponseHelper::success(array_merge(
            $request->user()->only(['id', 'name', 'email', 'school_id']),
            ['roles' => $request->user()->getRoleNames()]
        ), 'User retrieved successfully.');
    }

    /**
     * Login user - handles both web and API requests
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        // AuthService now expects email and password string
        $result = $this->authService->authCheck(
            $request->input('email'),
            $request->input('password')
        );

        // Always log the user into the web guard so that
        // web routes like /dashboard recognize the session
        // even when the login was performed via API/AJAX.
        if (isset($result['user'])) {
            Auth::guard('web')->login($result['user']);
        }

        // For web requests, redirect to dashboard
        if (!$request->expectsJson() && !$request->ajax()) {
            return redirect()->intended('/dashboard');
        }

        // For API/AJAX requests, return JSON with token
        return ResponseHelper::success($result, 'User logged in successfully');
    }

    /**
     * Logout the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // 1. Revoke the token that was used to authenticate the current request
        $user = $request->user();
        if ($user) {
            /** @var \Laravel\Sanctum\PersonalAccessToken $token */
            $token = $user->currentAccessToken();
            if ($token && !($token instanceof \Laravel\Sanctum\TransientToken)) {
                $token->delete();
            }
        }

        // 2. Clear the web session
        // We use Auth::guard('web') because the 'sanctum' guard (RequestGuard)
        // does not have a logout() method.
        Auth::guard('web')->logout();

        // 3. Invalidate and regenerate session tokens
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return ResponseHelper::success(null, 'User logged out successfully');
    }

    /**
     * Create a new user (registration) via AuthService.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        // Pass validated data array
        $result = $this->authService->create($request->validated());
        return ResponseHelper::success($result['user'], $result['message']);
    }

    /**
     * Update authenticated user's details via AuthService.
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request)
    {

        // Pass validated data array
        $user = $this->authService->updateDetails($request->validated());

        return ResponseHelper::success([
            'user' => $user
        ], 'Profile updated successfully');
    }

    /**
     * Reset password via AuthService.
     *
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $success = $this->authService->resetPassword(
            $request->email,
            $request->password,
            $request->token
        );

        if (!$success) {
            return ResponseHelper::error('Invalid or expired password reset token', 400);
        }

        return ResponseHelper::success(null, 'Password reset successfully');
    }

    /**
     * Create a new school via SchoolService.
     *
     * @param CreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSchool(CreateRequest $request)
    {
        $data = $request->validated();
        $school = $this->schoolService->createSchool($data);

        return ResponseHelper::success($school, 'School created successfully');
    }

    /**
     * Request password reset email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = $this->authService->sendPasswordResetLink($request->validated('email'));
        if ($status) {
            return ResponseHelper::success(null, 'Password reset link sent to your email');
        }
        return ResponseHelper::error('Unable to send password reset link', 400);
    }
}
