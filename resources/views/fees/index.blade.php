@extends('layouts.app')

@section('title', 'Manage Fees')
@section('header_title', 'Fees Management')

@section('content')
    <!-- Actions Toolbar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex flex-fill gap-2 w-100 w-md-75">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="feeSearch" class="form-control border-start-0 ps-0" placeholder="Search fees..."
                    oninput="reloadFees()">
            </div>
            <select id="filterClass" class="form-select w-auto" onchange="reloadFees()">
                <option value="">All Classes</option>
            </select>
            <select id="filterTerm" class="form-select w-auto" onchange="reloadFees()">
                <option value="">All Terms</option>
            </select>
        </div>

        @hasrole('super_admin|school_admin')
            <button type="button" class="btn btn-primary-premium flex-shrink-0"
                onclick="App.resetForm(document.forms['createFeeForm']);" data-bs-toggle="modal"
                data-bs-target="#createFeeModal">
                <i class="bi bi-plus-lg me-1"></i> Create Fee
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fee Title</th>
                            <th>Scope / Class</th>
                            <th>Amount</th>
                            <th>Term & Session</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="feesTableBody">
                        <!-- Content loaded via JS -->
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top text-center" id="paginationInfo">
                <small class="text-muted">Loading...</small>
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
                        <h5 class="modal-title fw-bold">Create New Fee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="vstack gap-3">
                            <div>
                                <label class="form-label">Fee Title *</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="e.g. First Term Tuition" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="amount" class="form-control" step="0.01"
                                            placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fee Type *</label>
                                    <select name="fee_type" id="feeTypeSelect" class="form-select" required>
                                        <option value="tuition">Tuition</option>
                                        <option value="exam">Exam</option>
                                        <option value="uniform">Uniform</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Session *</label>
                                    <select name="session_id" id="sessionSelect" class="form-select" required>
                                        <option value="">Select Session</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Term *</label>
                                    <select name="term_id" id="termSelect" class="form-select" required>
                                        <option value="">Select Term</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Target Class (Optional)</label>
                                <select name="class_id" id="classSelect" class="form-select">
                                    <option value="">All Classes (School-wide)</option>
                                </select>
                                <small class="text-muted">Leave empty to apply to all students in the school.</small>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Due Date *</label>
                                    <input type="date" name="due_date" class="form-control" required>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_mandatory"
                                            value="1" id="isMandatorySwitch" checked>
                                        <label class="form-check-label" for="isMandatorySwitch">Is Mandatory?</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Optional details..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Fee</button>
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
                        <h5 class="modal-title fw-bold">Edit Fee Definition</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="vstack gap-3">
                            <div>
                                <label class="form-label">Fee Title *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="amount" class="form-control" step="0.01"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fee Type *</label>
                                    <select name="fee_type" id="editFeeTypeSelect" class="form-select" required>
                                        <option value="tuition">Tuition</option>
                                        <option value="exam">Exam</option>
                                        <option value="uniform">Uniform</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Session *</label>
                                    <select name="session_id" id="editSessionSelect" class="form-select" required>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Term *</label>
                                    <select name="term_id" id="editTermSelect" class="form-select" required>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Target Class</label>
                                <select name="class_id" id="editClassSelect" class="form-select">
                                    <option value="">All Classes</option>
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Due Date *</label>
                                    <input type="date" name="due_date" class="form-control" required>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_mandatory"
                                            value="1" id="editIsMandatorySwitch">
                                        <label class="form-check-label" for="editIsMandatorySwitch">Is Mandatory?</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Fee</button>
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
                            <div class="fw-bold text-dark">${item.title}</div>
                            <small class="text-muted">${item.description || 'No description'}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">
                                <i class="bi bi-building me-1"></i>${item.class_room?.name || 'All Students'}
                            </span>
                        </td>
                        <td class="fw-bold text-primary">${App.formatCurrency(item.amount)}</td>
                        <td>
                            <div class="small">${item.term?.name || 'N/A'}</div>
                            <div class="extra-small text-muted">${item.session?.name || 'N/A'}</div>
                        </td>
                        <td class="text-capitalize small">${item.fee_type}</td>
                        <td>
                            <span class="badge rounded-pill bg-${item.is_mandatory ? 'danger' : 'info'}-subtle text-${item.is_mandatory ? 'danger' : 'info'} px-3">
                                ${item.is_mandatory ? 'Mandatory' : 'Optional'}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="/fees/assign?fee_id=${item.id}" class="btn btn-light shadow-sm btn-sm" title="Assign Students">
                                    <i class="bi bi-person-plus-fill text-success"></i>
                                </a>
                                <button class="btn btn-light shadow-sm btn-sm" 
                                    data-action="edit" data-entity="fee" data-id="${item.id}" title="Edit">
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                </button>
                                <button class="btn btn-light shadow-sm btn-sm" 
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
