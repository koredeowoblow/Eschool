<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasRoles, HasUuids, HasApiTokens;
    protected $guard_name = 'api';
    protected $fillable = [
        "name",
        "email",
        "password",
        "address",
        "city",
        "state",
        "zip",
        "country",
        "phone",
        "school_id",
        "profile_photo",
        "status",
        "last_login_at",
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_address',
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_login_at'     => 'datetime',
            'status'            => 'string',
        ];
    }

    /**
     * Get the user's full address.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get the user's profile photo URL.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return url('storage/' . $this->profile_photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->hasMany(Student::class);
    }

    public function guardian()
    {
        return $this->hasMany(Guardian::class);
    }

    public function teacher()
    {
        return $this->hasMany(TeacherProfile::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scoped Queries (school-wide access for admins/teachers)
    |--------------------------------------------------------------------------
    */
    public function schoolStudents()
    {
        return Student::whereHas('user', fn($q) => $q->where('school_id', $this->school_id));
    }

    public function schoolTeachers()
    {
        return TeacherProfile::whereHas('user', fn($q) => $q->where('school_id', $this->school_id));
    }

    public function schoolGuardians()
    {
        return Guardian::whereHas('user', fn($q) => $q->where('school_id', $this->school_id));
    }

    public function guardianStudents()
    {
        // a guardian may have multiple guardianships => flatten all students
        return $this->guardian()->with('students')->get()->pluck('students')->flatten();
    }

    /*
    |--------------------------------------------------------------------------
    | Role-based helpers
    |--------------------------------------------------------------------------
    */
    public function myStudents()
    {
        if ($this->hasRole('Student')) {
            return Student::where('user_id', $this->id)->get();
        }

        if ($this->hasRole('Guardian')) {
            return $this->guardianStudents();
        }

        if ($this->hasRole('Teacher') || $this->hasRole('School Admin')) {
            return $this->schoolStudents()->get();
        }

        return collect();
    }

    public function myStudentById($id)
    {
        return $this->myStudents()->where('id', $id)->first();
    }

    public function myTeachers()
    {
        if ($this->hasRole('Teacher')) {
            return $this->teacher()->get();
        }

        if ($this->hasRole('School Admin')) {
            return $this->schoolTeachers()->get();
        }

        return collect();
    }

    public function myTeacherById($id)
    {
        return $this->myTeachers()->where('id', $id)->first();
    }

    public function myGuardians()
    {
        if ($this->hasRole('Guardian')) {
            return $this->guardian()->get();
        }

        if ($this->hasRole('Student')) {
            return $this->student()->first()?->guardians ?? collect();
        }

        if ($this->hasRole('School Admin')) {
            return $this->schoolGuardians()->get();
        }

        return collect();
    }

    public function myGuardianById($id)
    {
        return $this->myGuardians()->where('id', $id)->first();
    }
}
