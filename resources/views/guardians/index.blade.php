@extends('layouts.app')

@section('title', 'Guardians')
@section('header_title', 'Guardians')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="guardianSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search guardians..."
                oninput="reloadGuardians()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.resetForm(document.forms['createGuardianForm']);"
                data-bs-toggle="modal" data-bs-target="#createGuardianModal">
                <i class="bi bi-plus-lg me-1"></i> Add Guardian
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Guardian</th>
                            <th>Phone / Occupation</th>
                            <th>Relation</th>
                            <th class="text-right">Actions</th>
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
                        <h5 class="modal-title font-bold">Add Guardian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="role" value="guardian">
                        <h6 class="font-bold  mb-6 ">Guardian Information</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-12 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Full Name *</label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="John Doe" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Email *</label>
                                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="guardian@example.com"
                                    required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Phone</label>
                                <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="+234 xxx xxx xxxx">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Relation *</label>
                                <select name="relation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
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
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Occupation *</label>
                                <input type="text" name="occupation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="e.g. Engineer"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Guardian</button>
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
                        <h5 class="modal-title font-bold">Edit Guardian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="font-bold  mb-6 ">Guardian Information</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-12 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Full Name *</label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Email *</label>
                                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Phone</label>
                                <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Relation *</label>
                                <select name="relation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
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
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Occupation *</label>
                                <input type="text" name="occupation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Guardian</button>
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
