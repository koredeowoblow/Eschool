@extends('layouts.app')

@section('title', 'Assign Fees')
@section('header_title', 'Fee Assignment')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4  justify-center ">
        <div class="col-md-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header bg-white border-bottom py-3">
                    <h5 class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-title font-bold  mb-6 ">Assign Fee to Students</h5>
                    <p class="text-gray-500 small  mb-6 ">Select a fee and target to distribute it to students.</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 ">
                    <form id="assignFeeForm" action="/api/v1/fees/assign" method="POST"
                        onsubmit="App.submitForm(event, onAssignSuccess, 'feeAssignment')">
                        @csrf
                        <div class="vstack gap-4">
                            <!-- Fee Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6  font-bold">1. Select Fee *</label>
                                <select name="fee_id" id="feeSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Choose a fee definition...</option>
                                </select>
                            </div>

                            <hr class="my-0">

                            <!-- Target Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6  font-bold">2. Assignment Type</label>
                                <div class=" flex  gap-3 mt-4">
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
                                <label class="block text-sm font-medium text-gray-700  mb-6  font-bold">3. Select Class *</label>
                                <select name="class_id" id="classSelectAssignment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">Select a class...</option>
                                </select>
                                <small class="text-gray-500">The fee will be applied to all students currently enrolled in this
                                    class.</small>
                            </div>

                            <!-- Student Selection -->
                            <div id="studentTargetContainer" class=" hidden ">
                                <label class="block text-sm font-medium text-gray-700  mb-6  font-bold">3. Select Student *</label>
                                <select name="student_id" id="studentSelectAssignment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">Select a student...</option>
                                </select>
                                <small class="text-gray-500">Type to search for a specific student.</small>
                            </div>

                            <div class="alert alert-info border-0 shadow-sm flex items-center gap-3">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                                <div>
                                    Existing assignments for the same fee/student will be skipped to prevent duplicates.
                                </div>
                            </div>

                            <div class=" flex   justify-end  gap-2 mt-4">
                                <a href="{{ route('web.fees.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium">Cancel</a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium px-4">
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
