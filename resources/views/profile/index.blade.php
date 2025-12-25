@extends('layouts.app')

@section('title', 'My Profile')
@section('header_title', 'My Profile')

@section('content')
    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Column: Profile Header --}}
        <div class="col-lg-4">
            <div class="card-premium text-center p-4">
                <div class="mb-3">
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                        class="rounded-circle border border-3 border-primary" width="120" height="120"
                        style="object-fit: cover;">
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">
                    <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                </p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @foreach ($user->roles as $role)
                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                    @endforeach
                </div>
                @if ($user->school)
                    <p class="small text-muted mb-0">
                        <i class="bi bi-building me-1"></i>{{ $user->school->name }}
                    </p>
                @endif
                <hr class="my-3">
                <p class="small text-muted mb-1">Member Since</p>
                <p class="fw-semibold">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Right Column: Editable Forms --}}
        <div class="col-lg-8">
            {{-- Personal Information --}}
            <div class="card-premium p-4 mb-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-person-circle me-2 text-primary"></i>Personal Information
                </h5>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Full Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $user->phone) }}" placeholder="+234 xxx xxx xxxx">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male
                                </option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>
                                    Female</option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>
                                    Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth"
                                class="form-control @error('date_of_birth') is-invalid @enderror"
                                value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary-premium">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Address Information --}}
            <div class="card-premium p-4 mb-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-geo-alt me-2 text-primary"></i>Address Information
                </h5>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Hidden fields to preserve other data --}}
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Street Address</label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $user->address) }}" placeholder="123 Main Street">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city', $user->city) }}" placeholder="Lagos">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">State/Province</label>
                            <input type="text" name="state"
                                class="form-control @error('state') is-invalid @enderror"
                                value="{{ old('state', $user->state) }}" placeholder="Lagos State">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ZIP/Postal Code</label>
                            <input type="text" name="zip" class="form-control @error('zip') is-invalid @enderror"
                                value="{{ old('zip', $user->zip) }}" placeholder="100001">
                            @error('zip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" name="country"
                                class="form-control @error('country') is-invalid @enderror"
                                value="{{ old('country', $user->country) }}" placeholder="Nigeria">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary-premium">
                            <i class="bi bi-save me-2"></i>Save Address
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="card-premium p-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-shield-lock me-2 text-primary"></i>Change Password
                </h5>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Current Password *</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password *</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" minlength="8" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm New Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8"
                                required>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key me-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
