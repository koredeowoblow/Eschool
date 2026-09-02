@extends('layouts.app')

@section('title', 'Timetables')
@section('header_title', 'Class Schedules')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class=" flex  gap-2 w-100 w-md-auto">
            <select id="classSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white w-100 w-md-auto" style="min-width: 200px;" onchange="loadTimetable()">
                <option value="">Loading...</option>
            </select>
        </div>
        @hasrole('super_admin|School Admin|Teacher')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium w-100 w-md-auto" data-bs-toggle="modal"
                data-bs-target="#createSlotModal">
                <i class="bi bi-plus-lg me-1"></i> Add Slot
            </button>
        @endhasrole
    </div>

    <!-- Calendar Grid Timetable -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div id="timetableContainer" class="p-4">
                <div class=" text-center  py-5 text-gray-500">
                    <i class="bi bi-calendar3 fs-1  mb-6  d-block"></i>
                    <p>Select a class to view the weekly schedule</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Slot Modal -->
    <div class="modal fade" id="createSlotModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/api/v1/timetables" method="POST"
                    onsubmit="App.submitForm(event, loadTimetable, 'timetable', 'createSlotModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Add Timetable Slot</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Class</label>
                            <select name="class_room_id" id="create_slot_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject</label>
                            <select name="subject_id" id="create_slot_subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                <option>Select Class First</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Day</label>
                            <select name="day" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class="col-6">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Start Time</label>
                                <input type="time" name="start_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class="col-6">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">End Time</label>
                                <input type="time" name="end_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Slot</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.loadOptions('/api/v1/classes', 'classSelect', 'id', 'name', 'Select Class...');

            // Initialize Modal Dropdowns
            const createModal = document.getElementById('createSlotModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'create_slot_class_id');
            });

            // Dependent Dropdown for Slot Creation
            document.getElementById('create_slot_class_id').addEventListener('change', function() {
                const classId = this.value;
                if (classId) {
                    App.loadOptions(`/api/v1/classes/${classId}/subjects`, 'create_slot_subject_id', 'id',
                        'name', 'Select Subject');
                }
            });
        });

        function loadTimetable() {
            const classId = document.getElementById('classSelect').value;
            const container = document.getElementById('timetableContainer');

            if (!classId) {
                container.innerHTML = `
                    <div class=" text-center  py-5 text-gray-500">
                        <i class="bi bi-calendar3 fs-1  mb-6  d-block"></i>
                        <p>Select a class to view the weekly schedule</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = `
                <div class=" text-center  py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-gray-500 mt-4">Loading timetable...</p>
                </div>
            `;

            axios.get(`/api/v1/timetables?class_id=${classId}`)
                .then(res => {
                    const slots = res?.data?.data || [];
                    renderTimetableGrid(container, slots);
                })
                .catch(err => {
                    console.error('Timetable load error:', err);
                    container.innerHTML = `
                        <div class=" text-center  py-5 text-danger">
                            <i class="bi bi-exclamation-triangle fs-1  mb-6  d-block"></i>
                            <p>Failed to load timetable</p>
                        </div>
                    `;
                });
        }

        function renderTimetableGrid(container, slots) {
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            const timeSlots = generateTimeSlots();

            // Group slots by time and day
            const slotMap = {};
            slots.forEach(slot => {
                const key = `${slot.day}-${slot.start_time}`;
                slotMap[key] = slot;
            });

            let gridHTML = `
                <div class="timetable-grid">
                    <div class="timetable-header">
                        <div class="time-column-header">Time</div>
                        ${days.map(day => `<div class="day-header">${day}</div>`).join('')}
                    </div>
            `;

            timeSlots.forEach(time => {
                gridHTML += '<div class="time-slot-row">';
                gridHTML += `<div class="time-label">${time.label}</div>`;

                days.forEach(day => {
                    const key = `${day}-${time.start}`;
                    const slot = slotMap[key];

                    if (slot) {
                        gridHTML += `
                            <div class="timetable-slot">
                                <div class="subject-block" onclick="viewSlotDetails(${slot.id})">
                                    <div class="subject-name">${slot.subject?.name || 'N/A'}</div>
                                    <div class="teacher-name">${slot.teacher?.user?.name || 'N/A'}</div>
                                    <div class="subject-block-actions">
                                        @hasrole('super_admin|School Admin|Teacher')
                                        <button class="btn px-3 py-1.5 text-sm btn-outline-danger" onclick="event.stopPropagation(); deleteSlot(${slot.id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endhasrole
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        gridHTML += '<div class="timetable-slot empty"></div>';
                    }
                });

                gridHTML += '</div>';
            });

            gridHTML += '</div>';
            container.innerHTML = gridHTML;
        }

        function generateTimeSlots() {
            return [{
                    start: '08:00:00',
                    label: '08:00 - 09:00'
                },
                {
                    start: '09:00:00',
                    label: '09:00 - 10:00'
                },
                {
                    start: '10:00:00',
                    label: '10:00 - 10:30'
                }, // Break
                {
                    start: '10:30:00',
                    label: '10:30 - 11:30'
                },
                {
                    start: '11:30:00',
                    label: '11:30 - 12:30'
                },
                {
                    start: '12:30:00',
                    label: '12:30 - 13:30'
                }, // Lunch
                {
                    start: '13:30:00',
                    label: '13:30 - 14:30'
                },
                {
                    start: '14:30:00',
                    label: '14:30 - 15:30'
                }
            ];
        }

        function viewSlotDetails(id) {
            // Placeholder for viewing slot details
            // console.log('View slot:', id);
        }

        function deleteSlot(id) {
            App.deleteItem(`/api/v1/timetables/${id}`, loadTimetable);
        }
    </script>
@endsection
