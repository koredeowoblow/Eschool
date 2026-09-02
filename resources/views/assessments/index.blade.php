@extends('layouts.app')

@section('title', 'Assessments')
@section('header_title', 'Assessments')

@section('content')
<div x-data="assessmentsPage()" x-init="init()" class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input 
                type="text" 
                x-model="searchQuery" 
                @input.debounce.300ms="fetchAssessments()"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                placeholder="Search assessments..."
            >
        </div>

        @hasrole('super_admin|School Admin|Teacher')
        <button 
            @click="$dispatch('open-modal', 'createAssessmentModal')"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center gap-2 font-medium shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Assessment
        </button>
        @endhasrole
    </div>

    <!-- Data Table / Empty State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
        
        <!-- Loading Overlay for Table -->
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10" style="display: none;">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <template x-if="items.length === 0 && !loading">
            <x-empty-state 
                title="No assessments found" 
                message="Get started by creating your first assessment. It only takes a minute!"
                buttonText="Create First Assessment"
                buttonAction="$dispatch('open-modal', 'createAssessmentModal')"
            />
        </template>

        <template x-if="items.length > 0">
            <div>
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-gray-900">Title</th>
                                <th class="px-6 py-4 font-semibold text-gray-900">Class</th>
                                <th class="px-6 py-4 font-semibold text-gray-900">Term</th>
                                <th class="px-6 py-4 font-semibold text-gray-900">Max Score</th>
                                <th class="px-6 py-4 font-semibold text-gray-900 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="item in items" :key="item.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900" x-text="item.title"></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="item.class_room?.grade?.name + ' ' + (item.class_room?.section?.name || '')"></span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600" x-text="item.term?.name"></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1 text-gray-900 font-medium">
                                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            <span x-text="item.max_score"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="edit(item)" aria-label="Edit assessment" title="Edit" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button @click="confirmDelete(item)" aria-label="Delete assessment" title="Delete" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden divide-y divide-gray-100">
                    <template x-for="item in items" :key="item.id">
                        <div x-data="{ expanded: false }" class="p-4 bg-white transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900" x-text="item.title"></h3>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="item.class_room?.grade?.name"></span>
                                    </div>
                                </div>
                                <button @click="expanded = !expanded" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                                    <svg class="w-5 h-5 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                            
                            <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Term:</span>
                                    <span class="font-medium text-gray-900" x-text="item.term?.name"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Max Score:</span>
                                    <span class="font-medium text-gray-900" x-text="item.max_score"></span>
                                </div>
                                <div class="flex gap-2 pt-2">
                                    <button @click="edit(item)" class="flex-1 px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">Edit</button>
                                    <button @click="confirmDelete(item)" class="flex-1 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Delete</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Create Modal -->
    <x-modal id="createAssessmentModal" title="Create Assessment">
        <form @submit.prevent="submitForm('create')" x-data="{ loading: false }">
            <div class="space-y-4">
                <x-form-input name="title" label="Title" required />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                        <select x-model="form.class_room_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                            <option value="">Select Class</option>
                            <template x-for="c in options.classes" :key="c.id">
                                <option :value="c.id" x-text="c.grade?.name + ' (' + (c.section?.name || 'Main') + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                        <select x-model="form.term_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                            <option value="">Select Term</option>
                            <template x-for="t in options.terms" :key="t.id">
                                <option :value="t.id" x-text="t.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <x-form-input name="max_score" label="Maximum Score" type="number" required />
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="$dispatch('close-modal', 'createAssessmentModal')" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">Cancel</button>
                <x-button type="submit" x-bind:disabled="loading" x-bind:loading="loading">Save Assessment</x-button>
            </div>
        </form>
    </x-modal>

    <!-- Edit Modal -->
    <x-modal id="editAssessmentModal" title="Edit Assessment">
        <form @submit.prevent="submitForm('edit')" x-data="{ loading: false }">
            <div class="space-y-4">
                <x-form-input name="title" label="Title" required />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                        <select x-model="form.class_room_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                            <option value="">Select Class</option>
                            <template x-for="c in options.classes" :key="c.id">
                                <option :value="c.id" x-text="c.grade?.name + ' (' + (c.section?.name || 'Main') + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                        <select x-model="form.term_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
                            <option value="">Select Term</option>
                            <template x-for="t in options.terms" :key="t.id">
                                <option :value="t.id" x-text="t.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <x-form-input name="max_score" label="Maximum Score" type="number" required />
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="$dispatch('close-modal', 'editAssessmentModal')" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">Cancel</button>
                <x-button type="submit" x-bind:disabled="loading" x-bind:loading="loading">Update Assessment</x-button>
            </div>
        </form>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal id="deleteAssessmentModal" title="Delete Assessment">
        <div class="py-4">
            <div class="flex items-center gap-4  mb-6 ">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 -6a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-lg font-medium text-gray-900">Are you sure?</h4>
                    <p class="text-sm text-gray-500">This action cannot be undone. This will permanently delete the assessment.</p>
                </div>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="text-sm text-gray-600">Item to delete:</span>
                <p class="font-medium text-gray-900" x-text="itemToDelete?.title"></p>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <button @click="$dispatch('close-modal', 'deleteAssessmentModal')" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">Cancel</button>
            <button @click="executeDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium shadow-sm flex items-center gap-2">
                <span x-show="!deleteLoading">Yes, delete it</span>
                <span x-show="deleteLoading" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Deleting...
                </span>
            </button>
        </div>
    </x-modal>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assessmentsPage', () => ({
            items: [],
            searchQuery: '',
            loading: true,
            deleteLoading: false,
            itemToDelete: null,
            options: { classes: [], terms: [] },
            
            // Form state
            form: { id: null, title: '', class_room_id: '', term_id: '', max_score: '' },
            errors: {},
            touched: {},

            async init() {
                await Promise.all([
                    this.fetchAssessments(),
                    this.fetchOptions()
                ]);
            },

            async fetchOptions() {
                try {
                    const [resClasses, resTerms] = await Promise.all([
                        fetch('/api/v1/classes', { headers: { 'Accept': 'application/json' } }),
                        fetch('/api/v1/terms', { headers: { 'Accept': 'application/json' } })
                    ]);
                    const dataClasses = await resClasses.json();
                    const dataTerms = await resTerms.json();
                    this.options.classes = dataClasses.data || dataClasses;
                    this.options.terms = dataTerms.data || dataTerms;
                } catch (e) {
                    console.error("Failed to fetch options", e);
                }
            },

            async fetchAssessments() {
                this.loading = true;
                try {
                    const query = this.searchQuery ? `?search=${encodeURIComponent(this.searchQuery)}` : '';
                    const res = await fetch(`/api/v1/assessments${query}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    this.items = data.data || data;
                } catch (e) {
                    window.showToast('Failed to load assessments', 'error');
                } finally {
                    this.loading = false;
                }
            },

            validateField(field) {
                this.touched[field] = true;
                this.errors[field] = '';
                
                if (field === 'title' && !this.form.title) this.errors[field] = 'Title is required';
                if (field === 'max_score' && (!this.form.max_score || this.form.max_score <= 0)) this.errors[field] = 'Score must be greater than 0';
            },

            resetForm() {
                this.form = { id: null, title: '', class_room_id: '', term_id: '', max_score: '' };
                this.errors = {};
                this.touched = {};
            },

            edit(item) {
                this.resetForm();
                this.form = { ...item };
                this.$dispatch('open-modal', 'editAssessmentModal');
            },

            async submitForm(mode) {
                // Validate all
                ['title', 'max_score'].forEach(f => this.validateField(f));
                if (Object.values(this.errors).some(e => e !== '')) {
                    window.showToast('Please fix validation errors', 'warning');
                    return;
                }

                // Call from the x-data scope of the modal form
                const loadingRef = this.$event.target.__x.$data;
                loadingRef.loading = true;

                try {
                    const url = mode === 'edit' ? `/api/v1/assessments/${this.form.id}` : '/api/v1/assessments';
                    const method = mode === 'edit' ? 'PUT' : 'POST';
                    
                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        if (res.status === 422) {
                            this.errors = data.errors || {};
                            window.showToast('Please check the form for errors', 'error');
                        } else {
                            window.showToast(data.message || 'An error occurred', 'error');
                        }
                        return;
                    }

                    window.showToast(`Assessment ${mode === 'edit' ? 'updated' : 'created'} successfully`, 'success');
                    this.$dispatch('close-modal', mode === 'edit' ? 'editAssessmentModal' : 'createAssessmentModal');
                    this.fetchAssessments();
                    if(mode === 'create') this.resetForm();
                } catch (e) {
                    window.showToast('Network error', 'error');
                } finally {
                    loadingRef.loading = false;
                }
            },

            confirmDelete(item) {
                this.itemToDelete = item;
                this.$dispatch('open-modal', 'deleteAssessmentModal');
            },

            async executeDelete() {
                if(!this.itemToDelete) return;
                this.deleteLoading = true;
                
                try {
                    const res = await fetch(`/api/v1/assessments/${this.itemToDelete.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if(!res.ok) throw new Error('Failed to delete');
                    
                    window.showToast('Assessment deleted successfully', 'success');
                    this.$dispatch('close-modal', 'deleteAssessmentModal');
                    this.fetchAssessments();
                } catch (e) {
                    window.showToast('Failed to delete assessment', 'error');
                } finally {
                    this.deleteLoading = false;
                    this.itemToDelete = null;
                }
            }
        }));
    });
</script>
@endsection
