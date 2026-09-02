@extends('layouts.app')

@section('title', 'Settings')
@section('header_title', 'Settings')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-6 ">
        <div class=" md:col-span-6 col-span-1   lg:col-span-4 col-span-1 ">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 p-4">
                <h6 class="text-gray-500 text-uppercase small font-bold  mb-6 ">Academic Structure</h6>
                <p class="text-gray-500 small  mb-6 ">Configure grades, subjects, sections, sessions and terms.</p>
                <button class="btn px-3 py-1.5 text-sm btn-outline-primary" onclick="openSettingsPanel('academic')">Manage</button>
            </div>
        </div>
        <div class=" md:col-span-6 col-span-1   lg:col-span-4 col-span-1 ">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 p-4">
                <h6 class="text-gray-500 text-uppercase small font-bold  mb-6 ">Fees & Billing</h6>
                <p class="text-gray-500 small  mb-6 ">Manage fee types and invoice item templates.</p>
                <button class="btn px-3 py-1.5 text-sm btn-outline-primary" onclick="openSettingsPanel('fees')">Manage</button>
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
                <form id="createGradeForm" action="/api/v1/grades" method="POST"
                    onsubmit="App.submitForm(event, renderAcademicTables, 'grade', 'createGradeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">New Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editGradeForm" method="POST"
                    onsubmit="App.submitForm(event, renderAcademicTables, 'grade', 'editGradeModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Section Modals -->
    <div class="modal fade" id="createSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createSectionForm" action="/api/v1/sections" method="POST"
                    onsubmit="App.submitForm(event, renderAcademicTables, 'section', 'createSectionModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">New Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSectionForm" method="POST"
                    onsubmit="App.submitForm(event, renderAcademicTables, 'section', 'editSectionModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Session Modals -->
    <div class="modal fade" id="createSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createSessionForm" action="/api/v1/school-sessions" method="POST"
                    onsubmit="App.submitForm(event, renderAcademicTables, 'session', 'createSessionModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">New Session</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSessionForm" method="POST"
                    onsubmit="App.submitForm(event, renderAcademicTables, 'session', 'editSessionModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Session</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Term Modals -->
    <div class="modal fade" id="createTermModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createTermForm" action="/api/v1/terms" method="POST"
                    onsubmit="App.submitForm(event, renderAcademicTables, 'term', 'createTermModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">New Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTermModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTermForm" method="POST"
                    onsubmit="App.submitForm(event, renderAcademicTables, 'term', 'editTermModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fee Type Modals -->
    <div class="modal fade" id="createFeeTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createFeeTypeForm" action="/api/v1/fee-types" method="POST"
                    onsubmit="App.submitForm(event, renderFeeSettings, 'feeType', 'createFeeTypeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">New Fee Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editFeeTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFeeTypeForm" method="POST"
                    onsubmit="App.submitForm(event, renderFeeSettings, 'feeType', 'editFeeTypeModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Fee Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Invoice Item Modals -->
    <div class="modal fade" id="createInvoiceItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createInvoiceItemForm" action="/api/v1/invoice-items" method="POST"
                    onsubmit="App.submitForm(event, renderFeeSettings, 'invoiceItem', 'createInvoiceItemModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">New Invoice Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                            <input type="text" name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editInvoiceItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editInvoiceItemForm" method="POST"
                    onsubmit="App.submitForm(event, renderFeeSettings, 'invoiceItem', 'editInvoiceItemModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Invoice Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                            <input type="text" name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update</button>
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header flex flex-col md:flex-row  justify-between   items-center  gap-2">
                    <h6 class=" mb-6  font-bold">Grades</h6>
                    <button class="btn px-3 py-1.5 text-sm btn-primary-premium w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createGradeModal">New Grade</button>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-premium  mb-6 ">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="gradesTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header flex flex-col md:flex-row  justify-between   items-center  border-top gap-2">
                    <h6 class=" mb-6  font-bold">Sections</h6>
                    <button class="btn px-3 py-1.5 text-sm btn-primary-premium w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createSectionModal">New Section</button>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-premium  mb-6 ">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sectionsTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header flex flex-col md:flex-row  justify-between   items-center  border-top gap-2">
                    <h6 class=" mb-6  font-bold">Sessions</h6>
                    <button class="btn px-3 py-1.5 text-sm btn-primary-premium w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createSessionModal">New Session</button>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-premium  mb-6 ">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sessionsTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header flex flex-col md:flex-row  justify-between   items-center  border-top gap-2">
                    <h6 class=" mb-6  font-bold">Terms</h6>
                    <button class="btn px-3 py-1.5 text-sm btn-primary-premium w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createTermModal">New Term</button>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-premium  mb-6 ">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-right">Actions</th>
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header flex flex-col md:flex-row  justify-between   items-center  gap-2">
                    <h6 class=" mb-6  font-bold">Fee Types</h6>
                    <button class="btn px-3 py-1.5 text-sm btn-primary-premium w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createFeeTypeModal">New Fee Type</button>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-premium  mb-6 ">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="feeTypesTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header flex flex-col md:flex-row  justify-between   items-center  border-top gap-2">
                    <h6 class=" mb-6  font-bold">Invoice Items</h6>
                    <button class="btn px-3 py-1.5 text-sm btn-primary-premium w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createInvoiceItemModal">New Invoice Item</button>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-premium  mb-6 ">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="text-right">Actions</th>
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
