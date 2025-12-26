@extends('layouts.app')

@section('title', 'Assign Fees')
@section('header_title', 'Fee Assignment')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-premium">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold mb-0">Assign Fee to Students</h5>
                    <p class="text-muted small mb-0">Select a fee and target to distribute it to students.</p>
                </div>
                <div class="card-body">
                    <form id="assignFeeForm" action="/api/v1/fees/assign" method="POST"
                        onsubmit="App.submitForm(event, onAssignSuccess, 'feeAssignment')">
                        @csrf
                        <div class="vstack gap-4">
                            <!-- Fee Selection -->
                            <div>
                                <label class="form-label fw-bold">1. Select Fee *</label>
                                <select name="fee_id" id="feeSelect" class="form-select" required>
                                    <option value="">Choose a fee definition...</option>
                                </select>
                            </div>

                            <hr class="my-0">

                            <!-- Target Type -->
                            <div>
                                <label class="form-label fw-bold">2. Assignment Type</label>
                                <div class="d-flex gap-3 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" id="targetClass"
                                            value="class" checked>
                                        <label class="form-check-label" for="targetClass">Whole Class</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" id="targetStudent"
                                            value="student">
                                        <label class="form-check-label" for="targetStudent">Individual Student</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Class Selection -->
                            <div id="classTargetContainer">
                                <label class="form-label fw-bold">3. Select Class *</label>
                                <select name="class_id" id="classSelectAssignment" class="form-select">
                                    <option value="">Select a class...</option>
                                </select>
                                <small class="text-muted">The fee will be applied to all students currently enrolled in this
                                    class.</small>
                            </div>

                            <!-- Student Selection -->
                            <div id="studentTargetContainer" class="d-none">
                                <label class="form-label fw-bold">3. Select Student *</label>
                                <select name="student_id" id="studentSelectAssignment" class="form-select">
                                    <option value="">Select a student...</option>
                                </select>
                                <small class="text-muted">Type to search for a specific student.</small>
                            </div>

                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                                <div>
                                    Existing assignments for the same fee/student will be skipped to prevent duplicates.
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-2">
                                <a href="{{ route('web.fees.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary-premium px-4">
                                    <i class="bi bi-check2-circle me-1"></i> Process Assignment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Load Fees
            App.loadOptions('/api/v1/fees', 'feeSelect', new URLSearchParams(window.location.search).get('fee_id'),
                'id', 'title');

            // Load Classes
            App.loadOptions('/api/v1/classes', 'classSelectAssignment');

            // Load Students (could be many, maybe we need a better selector but for now simple)
            App.loadOptions('/api/v1/students', 'studentSelectAssignment', null, 'id', 'full_name');

            // Handle Target Switching
            const classTarget = document.getElementById('targetClass');
            const studentTarget = document.getElementById('targetStudent');
            const classContainer = document.getElementById('classTargetContainer');
            const studentContainer = document.getElementById('studentTargetContainer');
            const classSelect = document.getElementById('classSelectAssignment');
            const studentSelect = document.getElementById('studentSelectAssignment');

            classTarget.addEventListener('change', () => {
                classContainer.classList.remove('d-none');
                studentContainer.classList.add('d-none');
                studentSelect.required = false;
                classSelect.required = true;
            });

            studentTarget.addEventListener('change', () => {
                studentContainer.classList.remove('d-none');
                classContainer.classList.add('d-none');
                classSelect.required = false;
                studentSelect.required = true;
            });

            // Set initial required
            classSelect.required = true;
        });

        function onAssignSuccess(res) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: res.message,
                confirmButtonText: 'Back to List'
            }).then(() => {
                window.location.href = '/fees';
            });
        }
    </script>
@endsection
