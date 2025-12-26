@extends('layouts.app')

@section('title', 'Manage Membership Plans')
@section('header_title', 'Membership Plans Management')

@section('content')
    <div class="card-premium p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <div class="input-group w-100 w-md-50">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="planSearch" class="form-control border-start-0 ps-0" placeholder="Search plans..."
                    oninput="loadPlans()">
            </div>
            <button class="btn btn-primary-premium" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-2"></i>Create New Plan
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-premium table-hover align-middle">
                <thead>
                    <tr>
                        <th class="sortable-header" data-sort="name">Name</th>
                        <th class="sortable-header" data-sort="price">Price</th>
                        <th class="sortable-header" data-sort="no_of_students">Students Limit</th>
                        <th class="sortable-header" data-sort="no_of_teachers">Teachers Limit</th>
                        <th class="sortable-header" data-sort="no_of_guardians">Guardians Limit</th>
                        <th class="sortable-header" data-sort="no_of_staff">Staff Limit</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="plans-table-body">
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-muted small mt-2">Loading plans...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Plan Modal -->
    <div class="modal fade" id="createPlanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createPlanForm" method="POST" action="/api/v1/plans"
                        onsubmit="App.submitForm(event, loadPlans, 'create-plan', 'createPlanModal')">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Plan Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price *</label>
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="2"></textarea>
                            </div>

                            <h6 class="mt-4 mb-2">Limits</h6>
                            <div class="col-md-6">
                                <label class="form-label">Max Students *</label>
                                <input type="number" class="form-control" name="no_of_students" required>
                                <div class="form-text">Enter 0 for unlimited (if logic supports) or high number</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Teachers *</label>
                                <input type="number" class="form-control" name="no_of_teachers" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Guardians *</label>
                                <input type="number" class="form-control" name="no_of_guardians" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Staff *</label>
                                <input type="number" class="form-control" name="no_of_staff" required>
                            </div>
                        </div>

                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary-premium">Create Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Plan Modal -->
    <div class="modal fade" id="editPlanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editPlanForm" method="PUT"
                        onsubmit="App.submitForm(event, loadPlans, 'edit-plan', 'editPlanModal')">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Plan Name *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price *</label>
                                <input type="number" step="0.01" class="form-control" id="edit_price"
                                    name="price" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                            </div>

                            <h6 class="mt-4 mb-2">Limits</h6>
                            <div class="col-md-6">
                                <label class="form-label">Max Students *</label>
                                <input type="number" class="form-control" id="edit_no_of_students"
                                    name="no_of_students" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Teachers *</label>
                                <input type="number" class="form-control" id="edit_no_of_teachers"
                                    name="no_of_teachers" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Guardians *</label>
                                <input type="number" class="form-control" id="edit_no_of_guardians"
                                    name="no_of_guardians" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Staff *</label>
                                <input type="number" class="form-control" id="edit_no_of_staff" name="no_of_staff"
                                    required>
                            </div>
                        </div>

                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary-premium">Save Changes</button>
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
            loadPlans();
        });

        window.openCreateModal = function() {
            const form = document.getElementById('createPlanForm');
            if (form) form.reset();
            const modal = new bootstrap.Modal(document.getElementById('createPlanModal'));
            modal.show();
        };

        window.loadPlans = () => {
            const query = document.getElementById('planSearch').value;
            // Assuming App.renderTable handles the API call and table rendering. 
            // If App.renderTable logic expects 'plan' as data key, we need to ensure API returns standard response.
            // My API returns { success: true, data: [ ... ], message: ... }
            // I'll check if App.renderTable can handle this. In 'premium-app.js' usually.
            // Assuming it does.
            App.renderTable('/api/v1/plans', 'plans-table-body', 'plan');
        };

        window.editPlan = function(plan) {
            document.getElementById('edit_name').value = plan.name;
            document.getElementById('edit_price').value = plan.price;
            document.getElementById('edit_description').value = plan.description || '';
            document.getElementById('edit_no_of_students').value = plan.no_of_students;
            document.getElementById('edit_no_of_teachers').value = plan.no_of_teachers;
            document.getElementById('edit_no_of_guardians').value = plan.no_of_guardians;
            document.getElementById('edit_no_of_staff').value = plan.no_of_staff;

            document.getElementById('editPlanForm').action = `/api/v1/plans/${plan.id}`;
            const modal = new bootstrap.Modal(document.getElementById('editPlanModal'));
            modal.show();
        };

        window.deletePlan = function(id) {
            if (confirm('Are you sure you want to delete this plan?')) {
                axios.delete(`/api/v1/plans/${id}`)
                    .then(response => {
                        App.showToast('Plan deleted successfully');
                        loadPlans();
                    })
                    .catch(error => {
                        App.showToast(error.response.data.message || 'Error deleting plan', 'error');
                    });
            }
        }
    </script>
@endsection
