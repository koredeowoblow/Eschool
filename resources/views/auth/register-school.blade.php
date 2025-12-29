@extends('layouts.guest')

@section('title', 'School Registration')

@section('content')
    <style>
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, var(--color-primary-500, #3b82f6), var(--color-primary-700, #2563eb));
            padding: 3rem 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .register-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        }

        .section-title {
            color: var(--color-primary-600, #2563eb);
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
            background: rgba(59, 130, 246, 0.1);
            padding: 0.5rem;
            border-radius: 0.5rem;
        }

        .form-floating>.form-control {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
        }

        .form-floating>.form-control:focus {
            border-color: var(--color-primary-400, #3b82f6);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-floating>label {
            color: #64748b;
        }

        .btn-register {
            background: linear-gradient(135deg, var(--color-primary-500, #3b82f6), var(--color-primary-600, #2563eb));
            border: none;
            padding: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
        }
    </style>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="register-card">
                    <!-- Header -->
                    <div class="register-header">
                        <div class="position-relative z-1">
                            <i class="bi bi-mortarboard-fill mb-3 d-inline-block" style="font-size: 3rem;"></i>
                            <h2 class="fw-bold mb-1">Begin Your Journey</h2>
                            <p class="mb-0 opacity-75">Register your school today and transform your administration.</p>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form id="schoolRegistrationForm" method="POST"
                            action="{{ secure_url(route('school.register.submit', [], false)) }}">
                            @csrf

                            {{-- Hidden fields required by backend --}}
                            <input type="hidden" name="slug" id="slug">
                            <input type="hidden" name="admin_name" id="admin_name">
                            <input type="hidden" name="admin_email" id="admin_email">
                            <input type="hidden" name="status" value="pending">

                            <div class="row g-4">
                                <!-- Section 1: School Details -->
                                <div class="col-12">
                                    <div class="section-title">
                                        <i class="bi bi-building"></i> School Information
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="name" id="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name') }}" placeholder="School Name" required>
                                                <label>School Name <span class="text-danger">*</span></label>
                                            </div>
                                            @error('name')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" name="email" id="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ old('email') }}" placeholder="Official Email" required>
                                                <label>Official Email <span class="text-danger">*</span></label>
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" name="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    value="{{ old('phone') }}" placeholder="Phone Number" required>
                                                <label>Phone Number <span class="text-danger">*</span></label>
                                            </div>
                                            @error('phone')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="url" name="website"
                                                    class="form-control @error('website') is-invalid @enderror"
                                                    value="{{ old('website') }}" placeholder="Website URL">
                                                <label>Website URL (Optional)</label>
                                            </div>
                                            @error('website')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Location -->
                                <div class="col-12">
                                    <div class="section-title border-top pt-4">
                                        <i class="bi bi-geo-alt"></i> Location Details
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" style="height: 100px"
                                                    placeholder="Full Address" required>{{ old('address') }}</textarea>
                                                <label>Full Address <span class="text-danger">*</span></label>
                                            </div>
                                            @error('address')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="city"
                                                    class="form-control @error('city') is-invalid @enderror"
                                                    value="{{ old('city') }}" placeholder="City" required>
                                                <label>City <span class="text-danger">*</span></label>
                                            </div>
                                            @error('city')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="state"
                                                    class="form-control @error('state') is-invalid @enderror"
                                                    value="{{ old('state') }}" placeholder="State" required>
                                                <label>State <span class="text-danger">*</span></label>
                                            </div>
                                            @error('state')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" name="area"
                                                    class="form-control @error('area') is-invalid @enderror"
                                                    value="{{ old('area') }}" placeholder="Area">
                                                <label>Area/District (Optional)</label>
                                            </div>
                                            @error('area')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: Contact Person -->
                                <div class="col-12">
                                    <div class="section-title border-top pt-4">
                                        <i class="bi bi-person-badge"></i> Administrator Contact
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" name="contact_person" id="contact_person"
                                                    class="form-control @error('contact_person') is-invalid @enderror"
                                                    value="{{ old('contact_person') }}" placeholder="Contact Name"
                                                    required>
                                                <label>Admin Name <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="form-text small"><i class="bi bi-info-circle"></i> This person
                                                will
                                                be the default Super Admin.</div>
                                            @error('contact_person')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" name="contact_person_phone"
                                                    class="form-control @error('contact_person_phone') is-invalid @enderror"
                                                    value="{{ old('contact_person_phone') }}" placeholder="Direct Phone"
                                                    required>
                                                <label>Direct Phone <span class="text-danger">*</span></label>
                                            </div>
                                            @error('contact_person_phone')
                                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 4: Plan -->
                                <div class="col-12">
                                    <div class="section-title border-top pt-4">
                                        <i class="bi bi-credit-card"></i> Subscription Plan
                                    </div>
                                    <div class="form-floating">
                                        <select name="plan" id="create_plan"
                                            class="form-select @error('plan') is-invalid @enderror" required>
                                            <option value="">Loading Plans...</option>
                                        </select>
                                        <label>Select Your Plan <span class="text-danger">*</span></label>
                                    </div>
                                    @error('plan')
                                        <div class="invalid-feedback d-block small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-register w-100 text-white">
                                        <span class="fs-5">Submit Registration</span>
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>

                                <div class="col-12 text-center mt-3">
                                    <p class="text-muted">
                                        Already have an account? <a href="{{ route('login') }}"
                                            class="fw-bold text-decoration-none"
                                            style="color: var(--color-primary-600);">Login Here</a>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            App.loadOptions('/api/v1/plans', 'create_plan', @json(old('plan')), 'id', 'name',
                'Select a Plan');
        });

        document.getElementById('schoolRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Populate hidden fields
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const contactPerson = document.getElementById('contact_person').value;

            // Generate slug from name
            const slug = name.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');

            document.getElementById('slug').value = slug;
            document.getElementById('admin_name').value = contactPerson; // Default admin name to contact person
            document.getElementById('admin_email').value = email; // Default admin email to school email

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Welcome!',
                                text: 'Registration successful! Your school account has been created.',
                                confirmButtonText: 'Proceed to Login',
                                confirmButtonColor: '#3b82f6',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = "{{ route('login') }}";
                            });
                        } else {
                            alert('Registration successful!');
                            window.location.href = "{{ route('login') }}";
                        }
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            let errorMsg = '';
                            Object.values(data.errors).forEach(errors => {
                                errors.forEach(error => errorMsg += `• ${error}\n`);
                            });

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Failed',
                                    text: 'Please check the form for errors.',
                                    html: `<div class="text-start text-danger">${errorMsg.replace(/\n/g, '<br>')}</div>`,
                                    confirmButtonColor: '#3b82f6'
                                });
                            } else {
                                alert('Please fix the following errors:\n' + errorMsg);
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Registration Failed',
                                    text: data.message || 'Please try again later.',
                                    confirmButtonColor: '#3b82f6'
                                });
                            } else {
                                alert(data.message || 'Registration failed. Please try again.');
                            }
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnContent;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'An unexpected error occurred. Please try again.',
                            confirmButtonColor: '#3b82f6'
                        });
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                });
        });
    </script>
@endsection
