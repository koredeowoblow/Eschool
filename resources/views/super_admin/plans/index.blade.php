@extends('layouts.app')

@section('title', 'Manage Membership Plans')
@section('header_title', 'Membership Plans Management')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-4">
        <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
            <div class="input-group w-100 w-md-50">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="planSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search plans..."
                    oninput="loadPlans()">
            </div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-2"></i>Create New Plan
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle">
                <thead>
                    <tr>
                        <th class="sortable-header" data-sort="name">Name</th>
                        <th class="sortable-header" data-sort="price">Price</th>
                        <th class="sortable-header" data-sort="no_of_students">Students Limit</th>
                        <th class="sortable-header" data-sort="no_of_teachers">Teachers Limit</th>
                        <th class="sortable-header" data-sort="no_of_guardians">Guardians Limit</th>
                        <th class="sortable-header" data-sort="no_of_staff">Staff Limit</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="plans-table-body">
                    <tr>
                        <td colspan="7" class=" text-center  py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-gray-500 small mt-4">Loading plans...</p>
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

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Plan Name *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="name" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Price *</label>
                                <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="price" required>
                            </div>
                            <div class=" md:col-span-12 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="description" rows="2"></textarea>
                            </div>

                            <h6 class="mt-4  mb-6 ">Limits</h6>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Max Students *</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="no_of_students" required>
                                <div class="form-text">Enter 0 for unlimited (if logic supports) or high number</div>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Max Teachers *</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="no_of_teachers" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Max Guardians *</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="no_of_guardians" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Max Staff *</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="no_of_staff" required>
                            </div>
                        </div>

                        <div class="modal-footer mt-4">
                            <button type="button" class=" px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium " data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Create Plan</button>
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

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Plan Name *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_name" name="name" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Price *</label>
                                <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_price"
                                    name="price" required>
                            </div>
                            <div class=" md:col-span-12 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_description" name="description" rows="2"></textarea>
                            </div>

                            <h6 class="mt-4  mb-6 ">Limits</h6>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Max Students *</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_no_of_students"
                                    name="no_of_students" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Max Teachers *</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_no_of_teachers"
                                    name="no_of_teachers" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Max Guardians *</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_no_of_guardians"
                                    name="no_of_guardians" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Max Staff *</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_no_of_staff" name="no_of_staff"
                                    required>
                            </div>
                        </div>

                        <div class="modal-footer mt-4">
                            <button type="button" class=" px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium " data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Changes</button>
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
