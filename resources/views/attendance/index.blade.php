@extends('layouts.app')

@section('title', 'Attendance')
@section('header_title', 'Attendance Management')

@section('content')
    @hasrole('super_admin|School Admin|Teacher')
        <div class="card-premium mb-4">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Select Class</label>
                        <select id="classSelect" class="form-select" onchange="loadAttendance()">
                            <option value="">Loading...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Date</label>
                        <input type="date" id="dateInput" class="form-control" value="{{ date('Y-m-d') }}"
                            onchange="loadAttendance()">
                    </div>
                </div>
            </div>
        </div>
    @endhasrole

    <div class="card-premium">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="mb-0 fw-bold">Student List</h6>
        </div>

        <div class="card-body p-0">
            <form id="attendanceForm" action="/api/v1/attendance" method="POST"
                onsubmit="App.submitForm(event, loadAttendance, 'attendance', null)">
                <!-- Route to Store Attendance needed if real -->
                @csrf
                <div class="table-responsive">
                    <table class="table table-premium align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Late</th>
                                <th class="text-center">Absent</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Select a class to view list.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @hasrole('super_admin|School Admin|Teacher')
                    <div class="p-3 border-top text-end bg-light">
                        <button type="submit" class="btn btn-primary-premium px-4">Save Attendance</button>
                    </div>
                @endhasrole
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const classSelect = document.getElementById('classSelect');
            if (classSelect) {
                App.loadOptions('/api/v1/classes', 'classSelect', 'id', 'name', '-- Choose Class --');
            } else {
                // For students (no class select), load their own attendance immediately
                loadAttendance();
            }
        });

        function loadAttendance() {
            const classSelect = document.getElementById('classSelect');
            const dateInput = document.getElementById('dateInput');

            const classId = classSelect ? classSelect.value : '';
            const date = dateInput ? dateInput.value : '';

            // Construct URL - backend scoper handles student_id automatically if auth user is student
            let url = '/api/v1/attendance';
            const params = [];
            if (classId) params.push(`class_id=${classId}`);
            if (date) params.push(`date=${date}`);

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            App.renderTable(url, 'attendanceTableBody', 'attendance');
        }
    </script>
@endsection
