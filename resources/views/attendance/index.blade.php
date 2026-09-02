@extends('layouts.app')

@section('title', 'Attendance')
@section('header_title', 'Attendance Management')

@section('content')
    @hasrole('super_admin|School Admin|Teacher')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden  mb-6 ">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200  p-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4  align-items-end">
                    <div class=" md:col-span-4 col-span-1 ">
                        <label class="block text-sm font-medium text-gray-700  mb-6  small text-gray-500 text-uppercase font-bold">Select Class</label>
                        <select id="classSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" onchange="loadAttendance()">
                            <option value="">Loading...</option>
                        </select>
                    </div>
                    <div class=" md:col-span-4 col-span-1 ">
                        <label class="block text-sm font-medium text-gray-700  mb-6  small text-gray-500 text-uppercase font-bold">Date</label>
                        <input type="date" id="dateInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="{{ date('Y-m-d') }}"
                            onchange="loadAttendance()">
                    </div>
                </div>
            </div>
        </div>
    @endhasrole

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header bg-white border-bottom p-3">
            <h6 class=" mb-6  font-bold">Student List</h6>
        </div>

        <div class="p-0">
            <form id="attendanceForm" action="/api/v1/attendance" method="POST"
                onsubmit="App.submitForm(event, loadAttendance, 'attendance', null)">
                <!-- Route to Store Attendance needed if real -->
                @csrf
                <div class="overflow-x-auto">
                    <table class="table table-premium align-middle  mb-6 ">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th class=" text-center ">Present</th>
                                <th class=" text-center ">Late</th>
                                <th class=" text-center ">Absent</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <tr>
                                <td colspan="5" class=" text-center  py-5 text-gray-500">Select a class to view list.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @hasrole('super_admin|School Admin|Teacher')
                    <div class="p-3 border-top text-right bg-light">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium px-4">Save Attendance</button>
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
