<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Exception;

use function Laravel\Prompts\error;

/**
 * Service handling authentication and user profile/security operations.
 */
class AuthService
{

    public $otp;
    public function __construct(OtpService $otp)
    {
        $this->otp = $otp;
    }
    private const TOKEN_NAME = 'auth_token';

    /**
     * Attempt to authenticate a user and issue a token.
     *
     * @param string $email
     * @param string $password
     * @return array{user: User, token: string}
     *
     * @throws HttpResponseException
     */
    public function authCheck(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new HttpResponseException(get_error_response('Invalid credentials', 401));
        }

        // 1. User Status Check
        if ($user->status !== 'active') {
            throw new HttpResponseException(get_error_response('Your account is currently ' . ($user->status ?: 'pending') . '. Please contact your administrator.', 403));
        }

        // 2. School Activity Check (Bypass for Super Admins)
        $roles = $user->getRoleNames();
        $isSuperAdmin = $roles->contains('super_admin');

        if (!$isSuperAdmin && $user->school_id) {
            $school = $user->school;
            if (!$school || !$school->is_active) {
                throw new HttpResponseException(get_error_response('Your school account is currently inactive or pending review. Please contact support.', 403));
            }
        }

        $roles = $user->getRoleNames();
        $user['roles'] = $roles;

        // Sanctum personal access token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Create a new user and issue a token.
     *
     * @param array $data
     * @return array{user: User, message: string}
     */
    public function create(array $data): array
    {
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'display_name'  => $data['display_name'] ?? ($data['name'] ?? null),
            'password'      => Hash::make($data['password']),
            'gender'        => $data['gender'] ?? null,
            'phone_number'  => $data['phone_number'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address'       => $data['address'] ?? null,
        ]);

        $send = $this->otp->send($user->email, 'email_verification');
        $message = 'Verification email sent successfully';

        return compact('user', 'message');
    }


    /**
     * Send a password reset link to the given email.
     * 
     * @param string $email
     * @return bool
     */
    public function sendPasswordResetLink(string $email): bool
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT;
    }

    /**
     * Reset a user's password using email, password, and token.
     *
     * @param string $email
     * @param string $password
     * @param string $token
     * @return bool
     */
    public function resetPassword(string $email, string $password, string $token): bool
    {
        $status = Password::reset(
            ['email' => $email, 'password' => $password, 'password_confirmation' => $password, 'token' => $token],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET;
    }

    /**
     * Update the authenticated user's details.
     *
     * @param mixed $data Array or object with updatable fields.
     * @return JsonResponse
     */
    public function updateDetails($data): JsonResponse
    {
        $user = Auth::user();
        $user->update([
            'name'          => data_get($data, 'name', $user->name),
            'email'         => data_get($data, 'email', $user->email),
            'phone'         => data_get($data, 'phone', $user->phone),
            'display_name'  => data_get($data, 'display_name', $user->display_name),
            'gender'        => data_get($data, 'gender', $user->gender),
            'phone_number'  => data_get($data, 'phone_number', $user->phone_number),
            'date_of_birth' => data_get($data, 'date_of_birth', $user->date_of_birth),
            'address'       => data_get($data, 'address', $user->address),
        ]);

        $user->refresh();

        return response()->json([
            'message' => 'User details updated successfully',
            'user'    => $user,
        ], 200);
    }

    /**
     * Issue an auth token for the provided user.
     */
    private function issueToken(User $user): string
    {
        return $user->createToken(self::TOKEN_NAME)->plainTextToken;
    }


    /**
     * Assign one or multiple roles to a user.
     * Roles are specified by name.
     */
    public function assignRolesToUser(int $userId, array $roleNames): User
    {
        $user = $this->findUser($userId);
        $user->assignRole($roleNames);
        return $user->load('roles', 'permissions');
    }

    /**
     * Remove roles from a user (by name).
     */
    public function removeRolesFromUser(int $userId, array $roleNames): User
    {
        $user = $this->findUser($userId);
        $user->removeRole(...$roleNames);
        return $user->load('roles', 'permissions');
    }

    /**
     * Give (add) permissions directly to a user.
     */
    public function givePermissionsToUser(int $userId, $permissionNames): User
    {
        $user = $this->findUser($userId);
        $user->givePermissionTo($permissionNames);
        return $user->load('roles', 'permissions');
    }

    /**
     * Revoke permissions from a user.
     */
    public function revokePermissionsFromUser(int $userId, array $permissionNames): User
    {
        $user = $this->findUser($userId);
        $user->revokePermissionTo($permissionNames);
        return $user->load('roles', 'permissions');
    }

    /**
     * Helper: find user or throw.
     */
    protected function findUser(int $userId): User
    {
        $user = User::find($userId);
        if (! $user) {
            throw new Exception("User not found: {$userId}");
        }
        return $user;
    }

    // ... (other user methods)
}
