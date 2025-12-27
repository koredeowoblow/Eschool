@extends('layouts.app')

@section('title', 'Guardians')
@section('header_title', 'Guardians')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="guardianSearch" class="form-control border-start-0 ps-0" placeholder="Search guardians..."
                oninput="reloadGuardians()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createGuardianForm']);"
                data-bs-toggle="modal" data-bs-target="#createGuardianModal">
                <i class="bi bi-plus-lg me-1"></i> Add Guardian
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Guardian</th>
                            <th>Phone / Occupation</th>
                            <th>Relation</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="guardiansTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createGuardianModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createGuardianForm" action="/api/v1/guardians" method="POST"
                    onsubmit="App.submitForm(event, reloadGuardians, 'guardian', 'createGuardianModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Guardian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="role" value="guardian">
                        <h6 class="fw-bold mb-3">Guardian Information</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" placeholder="guardian@example.com"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+234 xxx xxx xxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Relation *</label>
                                <select name="relation" class="form-select" required>
                                    <option value="">Select Relation</option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                    <option value="uncle">Uncle</option>
                                    <option value="aunt">Aunt</option>
                                    <option value="grandfather">Grandfather</option>
                                    <option value="grandmother">Grandmother</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Occupation *</label>
                                <input type="text" name="occupation" class="form-control" placeholder="e.g. Engineer"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Guardian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editGuardianModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editGuardianForm" method="POST"
                    onsubmit="App.submitForm(event, reloadGuardians, 'guardian', 'editGuardianModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Guardian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="fw-bold mb-3">Guardian Information</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Relation *</label>
                                <select name="relation" class="form-select" required>
                                    <option value="">Select Relation</option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                    <option value="uncle">Uncle</option>
                                    <option value="aunt">Aunt</option>
                                    <option value="grandfather">Grandfather</option>
                                    <option value="grandmother">Grandmother</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Occupation *</label>
                                <input type="text" name="occupation" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Guardian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/guardians', 'guardiansTableBody', 'guardian');
        });

        function reloadGuardians() {
            const query = document.getElementById('guardianSearch').value;
            App.renderTable('/api/v1/guardians?search=' + encodeURIComponent(query), 'guardiansTableBody', 'guardian');
        }

        function editGuardian(data) {
            const form = document.getElementById('editGuardianForm');
            form.action = `/api/v1/guardians/${data.id}`;

            // Initialize formData
            const formData = {
                ...data,
                name: data.user?.name || data.name || '',
                email: data.user?.email || data.email || '',
                phone: data.user?.phone || '',
                relation: data.relation || '',
                occupation: data.occupation || ''
            };

            App.populateForm(form, formData);
            const modal = new bootstrap.Modal(document.getElementById('editGuardianModal'));
            modal.show();
        }
    </script>
@endsection
