@extends('layouts.guest')

@section('title', 'School Registration')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Register Your School</h2>
                            <p class="text-muted">Join our platform and start managing your school digitally</p>
                        </div>

                        <form id="schoolRegistrationForm" method="POST"
                            action="{{ secure_url(route('school.register.submit', [], false)) }}">
                            @csrf

                            {{-- Hidden fields required by backend --}}
                            <input type="hidden" name="slug" id="slug">
                            <input type="hidden" name="admin_name" id="admin_name">
                            <input type="hidden" name="admin_email" id="admin_email">
                            <input type="hidden" name="status" value="pending">

                            <div class="row g-3">
                                <!-- School Information -->
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">School Information</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">School Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="website"
                                        class="form-control @error('website') is-invalid @enderror"
                                        value="{{ old('website') }}" placeholder="https://yourschool.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Location -->
                                <div class="col-12 mt-4">
                                    <h5 class="border-bottom pb-2 mb-3">Location</h5>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" name="city"
                                        class="form-control @error('city') is-invalid @enderror"
                                        value="{{ old('city') }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <input type="text" name="state"
                                        class="form-control @error('state') is-invalid @enderror"
                                        value="{{ old('state') }}" required>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Area</label>
                                    <input type="text" name="area"
                                        class="form-control @error('area') is-invalid @enderror"
                                        value="{{ old('area') }}">
                                    @error('area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Contact Person -->
                                <div class="col-12 mt-4">
                                    <h5 class="border-bottom pb-2 mb-3">Contact Person</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Contact Person Name <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_person" id="contact_person"
                                        class="form-control @error('contact_person') is-invalid @enderror"
                                        value="{{ old('contact_person') }}" required>
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Contact Person Phone <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" name="contact_person_phone"
                                        class="form-control @error('contact_person_phone') is-invalid @enderror"
                                        value="{{ old('contact_person_phone') }}" required>
                                    @error('contact_person_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Plan Selection -->
                                <div class="col-12 mt-4">
                                    <h5 class="border-bottom pb-2 mb-3">Subscription Plan</h5>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Select Plan <span class="text-danger">*</span></label>
                                    <select name="plan" id="create_plan"
                                        class="form-select @error('plan') is-invalid @enderror" required>
                                        <option value="">Select Plan</option>
                                    </select>
                                    @error('plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        Submit Registration
                                    </button>
                                </div>

                                <div class="col-12 text-center mt-3">
                                    <p class="text-muted small">
                                        Already registered? <a href="{{ route('login') }}">Login here</a>
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
                'Select Plan');
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
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

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
                                title: 'Registration Successful!',
                                text: 'Your school will be reviewed and approved shortly.',
                                confirmButtonText: 'Go to Login'
                            }).then(() => {
                                window.location.href = "{{ route('login') }}";
                            });
                        } else {
                            alert(
                                'Registration successful! Your school will be reviewed and approved shortly.'
                            );
                            window.location.href = "{{ route('login') }}";
                        }
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            let errorMsg = 'Please fix the following errors:\n';
                            Object.values(data.errors).forEach(errors => {
                                errors.forEach(error => errorMsg += '- ' + error + '\n');
                            });
                            alert(errorMsg);
                        } else {
                            alert(data.message || 'Registration failed. Please try again.');
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Submit Registration';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit Registration';
                });
        });
    </script>
@endsection
