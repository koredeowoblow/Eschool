@extends('layouts.app')

@section('title', 'Manage Fees')
@section('header_title', 'Fees Management')

@section('content')
    <!-- Actions Toolbar -->
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class=" flex  flex-fill gap-2 w-100 w-md-75">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="feeSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search fees..."
                    oninput="reloadFees()">
            </div>
            <select id="filterClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white w-auto" onchange="reloadFees()">
                <option value="">All Classes</option>
            </select>
            <select id="filterTerm" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white w-auto" onchange="reloadFees()">
                <option value="">All Terms</option>
            </select>
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium flex-shrink-0"
                onclick="App.resetForm(document.forms['createFeeForm']);" data-bs-toggle="modal"
                data-bs-target="#createFeeModal">
                <i class="bi bi-plus-lg me-1"></i> Create Fee
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                    <thead>
                        <tr>
                            <th>Fee Title</th>
                            <th>Scope / Class</th>
                            <th>Amount</th>
                            <th>Term & Session</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="feesTableBody">
                        <!-- Content loaded via JS -->
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top  text-center " id="paginationInfo">
                <small class="text-gray-500">Loading...</small>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createFeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createFeeForm" action="/api/v1/fees" method="POST"
                    onsubmit="App.submitForm(event, reloadFees, 'fee', 'createFeeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Create New Fee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="vstack gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Fee Title *</label>
                                <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="e.g. First Term Tuition" required>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" step="0.01"
                                            placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Fee Type *</label>
                                    <select name="fee_type" id="feeTypeSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                        <option value="tuition">Tuition</option>
                                        <option value="exam">Exam</option>
                                        <option value="uniform">Uniform</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Session *</label>
                                    <select name="session_id" id="sessionSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                        <option value="">Select Session</option>
                                    </select>
                                </div>
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Term *</label>
                                    <select name="term_id" id="termSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                        <option value="">Select Term</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Target Class (Optional)</label>
                                <select name="class_id" id="classSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">All Classes (School-wide)</option>
                                </select>
                                <small class="text-gray-500">Leave empty to apply to all students in the school.</small>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Due Date *</label>
                                    <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                                </div>
                                <div class=" md:col-span-6 col-span-1   flex  align-items-end">
                                    <div class="form-check form-switch  mb-6 ">
                                        <input class="form-check-input" type="checkbox" name="is_mandatory"
                                            value="1" id="isMandatorySwitch" checked>
                                        <label class="form-check-label" for="isMandatorySwitch">Is Mandatory?</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                                <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="2" placeholder="Optional details..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Fee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editFeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFeeForm" method="POST"
                    onsubmit="App.submitForm(event, reloadFees, 'fee', 'editFeeModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Fee Definition</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="vstack gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Fee Title *</label>
                                <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" step="0.01"
                                            required>
                                    </div>
                                </div>
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Fee Type *</label>
                                    <select name="fee_type" id="editFeeTypeSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                        <option value="tuition">Tuition</option>
                                        <option value="exam">Exam</option>
                                        <option value="uniform">Uniform</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Session *</label>
                                    <select name="session_id" id="editSessionSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    </select>
                                </div>
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Term *</label>
                                    <select name="term_id" id="editTermSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Target Class</label>
                                <select name="class_id" id="editClassSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">All Classes</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Due Date *</label>
                                    <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                                </div>
                                <div class=" md:col-span-6 col-span-1   flex  align-items-end">
                                    <div class="form-check form-switch  mb-6 ">
                                        <input class="form-check-input" type="checkbox" name="is_mandatory"
                                            value="1" id="editIsMandatorySwitch">
                                        <label class="form-check-label" for="editIsMandatorySwitch">Is Mandatory?</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                                <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Fee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadFees();

            // Load filters
            App.loadOptions('/api/v1/classes', 'filterClass', null, 'id', 'name', 'All Classes');
            App.loadOptions('/api/v1/terms', 'filterTerm', null, 'id', 'name', 'All Terms');

            // Initialize Create Modal Dropdowns
            const createModal = document.getElementById('createFeeModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'classSelect', null, 'id', 'name',
                    'All Classes (School-wide)');
                App.loadOptions('/api/v1/school-sessions', 'sessionSelect');
                App.loadOptions('/api/v1/terms', 'termSelect');
            });
        });

        function reloadFees() {
            const query = document.getElementById('feeSearch').value;
            const classId = document.getElementById('filterClass').value;
            const termId = document.getElementById('filterTerm').value;

            let url = `/api/v1/fees?search=${encodeURIComponent(query)}`;
            if (classId) url += `&class_id=${classId}`;
            if (termId) url += `&term_id=${termId}`;

            App.renderTable(url, 'feesTableBody', (item) => {
                return App.safeHTML`
                    <tr>
                        <td>
                            <div class="font-bold text-dark">${item.title}</div>
                            <small class="text-gray-500">${item.description || 'No description'}</small>
                        </td>
                        <td>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="bi bi-building me-1"></i>${item.class_room?.name || 'All Students'}
                            </span>
                        </td>
                        <td class="font-bold text-primary">${App.formatCurrency(item.amount)}</td>
                        <td>
                            <div class="small">${item.term?.name || 'N/A'}</div>
                            <div class="extra-small text-gray-500">${item.session?.name || 'N/A'}</div>
                        </td>
                        <td class="text-capitalize small">${item.fee_type}</td>
                        <td>
                            <span class="badge rounded-pill bg-${item.is_mandatory ? 'danger' : 'info'}-subtle text-${item.is_mandatory ? 'danger' : 'info'} px-3">
                                ${item.is_mandatory ? 'Mandatory' : 'Optional'}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class=" flex   justify-end  gap-2">
                                <a href="/fees/assign?fee_id=${item.id}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm" title="Assign Students">
                                    <i class="bi bi-person-plus-fill text-success"></i>
                                </a>
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm" 
                                    data-action="edit" data-entity="fee" data-id="${item.id}" title="Edit">
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                </button>
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm" 
                                    data-action="delete" data-entity="fee" data-id="${item.id}" title="Delete">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        function editFee(data) {
            const form = document.getElementById('editFeeForm');
            form.action = `/api/v1/fees/${data.id}`;

            // Populate form
            App.populateForm(form, data);

            // Load options and select current
            App.loadOptions('/api/v1/classes', 'editClassSelect', data.class_id, 'id', 'name', 'All Classes');
            App.loadOptions('/api/v1/school-sessions', 'editSessionSelect', data.session_id);
            App.loadOptions('/api/v1/terms', 'editTermSelect', data.term_id);

            // Set checkbox
            document.getElementById('editIsMandatorySwitch').checked = !!data.is_mandatory;

            const modal = new bootstrap.Modal(document.getElementById('editFeeModal'));
            modal.show();
        }
    </script>
@endsection
