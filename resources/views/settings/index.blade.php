@extends('layouts.app')

@section('title', 'Settings')
@section('header_title', 'Settings')

@section('content')
    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="card-premium h-100 p-4">
                <h6 class="text-muted text-uppercase small fw-bold mb-2">Academic Structure</h6>
                <p class="text-muted small mb-3">Configure grades, subjects, sections, sessions and terms.</p>
                <button class="btn btn-sm btn-outline-primary" onclick="openSettingsPanel('academic')">Manage</button>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card-premium h-100 p-4">
                <h6 class="text-muted text-uppercase small fw-bold mb-2">Fees & Billing</h6>
                <p class="text-muted small mb-3">Manage fee types and invoice item templates.</p>
                <button class="btn btn-sm btn-outline-primary" onclick="openSettingsPanel('fees')">Manage</button>
            </div>
        </div>
    </div>

    <div class="mt-4" id="settingsDetail"></div>
@endsection

@section('modals')
    <!-- Grade Modals -->
    <div class="modal fade" id="createGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createGradeForm" action="/api/v1/grades" method="POST" onsubmit="App.submitForm(event, renderAcademicTables, 'grade', 'createGradeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">New Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editGradeForm" method="POST" onsubmit="App.submitForm(event, renderAcademicTables, 'grade', 'editGradeModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Section Modals -->
    <div class="modal fade" id="createSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createSectionForm" action="/api/v1/sections" method="POST" onsubmit="App.submitForm(event, renderAcademicTables, 'section', 'createSectionModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">New Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSectionForm" method="POST" onsubmit="App.submitForm(event, renderAcademicTables, 'section', 'editSectionModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Session Modals -->
    <div class="modal fade" id="createSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createSessionForm" action="/api/v1/school-sessions" method="POST" onsubmit="App.submitForm(event, renderAcademicTables, 'session', 'createSessionModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">New Session</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSessionForm" method="POST" onsubmit="App.submitForm(event, renderAcademicTables, 'session', 'editSessionModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Session</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Term Modals -->
    <div class="modal fade" id="createTermModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createTermForm" action="/api/v1/terms" method="POST" onsubmit="App.submitForm(event, renderAcademicTables, 'term', 'createTermModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">New Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTermModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTermForm" method="POST" onsubmit="App.submitForm(event, renderAcademicTables, 'term', 'editTermModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fee Type Modals -->
    <div class="modal fade" id="createFeeTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createFeeTypeForm" action="/api/v1/fee-types" method="POST" onsubmit="App.submitForm(event, renderFeeSettings, 'feeType', 'createFeeTypeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">New Fee Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editFeeTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFeeTypeForm" method="POST" onsubmit="App.submitForm(event, renderFeeSettings, 'feeType', 'editFeeTypeModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Fee Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Invoice Item Modals -->
    <div class="modal fade" id="createInvoiceItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createInvoiceItemForm" action="/api/v1/invoice-items" method="POST" onsubmit="App.submitForm(event, renderFeeSettings, 'invoiceItem', 'createInvoiceItemModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">New Invoice Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editInvoiceItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editInvoiceItemForm" method="POST" onsubmit="App.submitForm(event, renderFeeSettings, 'invoiceItem', 'editInvoiceItemModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Invoice Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openSettingsPanel(type) {
        if (type === 'academic') {
            renderAcademicTables();
        } else if (type === 'fees') {
            renderFeeSettings();
        }
    }

    function renderAcademicTables() {
        const container = document.getElementById('settingsDetail');
        container.innerHTML = `
            <div class="card-premium">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Grades</h6>
                    <button class="btn btn-sm btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createGradeModal">New Grade</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="gradesTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-header d-flex justify-content-between align-items-center border-top">
                    <h6 class="mb-0 fw-bold">Sections</h6>
                    <button class="btn btn-sm btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createSectionModal">New Section</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sectionsTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-header d-flex justify-content-between align-items-center border-top">
                    <h6 class="mb-0 fw-bold">Sessions</h6>
                    <button class="btn btn-sm btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createSessionModal">New Session</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sessionsTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-header d-flex justify-content-between align-items-center border-top">
                    <h6 class="mb-0 fw-bold">Terms</h6>
                    <button class="btn btn-sm btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createTermModal">New Term</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="termsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>`;

        App.renderTable('/api/v1/grades', 'gradesTableBody', 'grade');
        App.renderTable('/api/v1/sections', 'sectionsTableBody', 'section');
        App.renderTable('/api/v1/school-sessions', 'sessionsTableBody', 'session');
        App.renderTable('/api/v1/terms', 'termsTableBody', 'term');
    }

    function renderFeeSettings() {
        const container = document.getElementById('settingsDetail');
        container.innerHTML = `
            <div class="card-premium">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Fee Types</h6>
                    <button class="btn btn-sm btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createFeeTypeModal">New Fee Type</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="feeTypesTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-header d-flex justify-content-between align-items-center border-top">
                    <h6 class="mb-0 fw-bold">Invoice Items</h6>
                    <button class="btn btn-sm btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createInvoiceItemModal">New Invoice Item</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceItemsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>`;

        App.renderTable('/api/v1/fee-types', 'feeTypesTableBody', 'feeType');
        App.renderTable('/api/v1/invoice-items', 'invoiceItemsTableBody', 'invoiceItem');
    }

    function editGrade(data) {
        const form = document.getElementById('editGradeForm');
        form.action = `/api/v1/grades/${data.id}`;
        App.populateForm(form, data);
        new bootstrap.Modal(document.getElementById('editGradeModal')).show();
    }

    function editSection(data) {
        const form = document.getElementById('editSectionForm');
        form.action = `/api/v1/sections/${data.id}`;
        App.populateForm(form, data);
        new bootstrap.Modal(document.getElementById('editSectionModal')).show();
    }

    function editSession(data) {
        const form = document.getElementById('editSessionForm');
        form.action = `/api/v1/school-sessions/${data.id}`;
        App.populateForm(form, data);
        new bootstrap.Modal(document.getElementById('editSessionModal')).show();
    }

    function editTerm(data) {
        const form = document.getElementById('editTermForm');
        form.action = `/api/v1/terms/${data.id}`;
        App.populateForm(form, data);
        new bootstrap.Modal(document.getElementById('editTermModal')).show();
    }

    function editFeeType(data) {
        const form = document.getElementById('editFeeTypeForm');
        form.action = `/api/v1/fee-types/${data.id}`;
        App.populateForm(form, data);
        new bootstrap.Modal(document.getElementById('editFeeTypeModal')).show();
    }

    function editInvoiceItem(data) {
        const form = document.getElementById('editInvoiceItemForm');
        form.action = `/api/v1/invoice-items/${data.id}`;
        App.populateForm(form, data);
        new bootstrap.Modal(document.getElementById('editInvoiceItemModal')).show();
    }

    function deleteGrade(id) {
		App.deleteItem(`/api/v1/grades/${id}`, renderAcademicTables);
    }

    function deleteSection(id) {
		App.deleteItem(`/api/v1/sections/${id}`, renderAcademicTables);
    }

    function deleteSession(id) {
		App.deleteItem(`/api/v1/school-sessions/${id}`, renderAcademicTables);
    }

    function deleteTerm(id) {
		App.deleteItem(`/api/v1/terms/${id}`, renderAcademicTables);
    }

    function deleteFeeType(id) {
		App.deleteItem(`/api/v1/fee-types/${id}`, renderFeeSettings);
    }

    function deleteInvoiceItem(id) {
        App.deleteItem(`/api/v1/invoice-items/${id}`, renderFeeSettings);
    }
</script>
@endsection
