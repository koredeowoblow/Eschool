@extends('layouts.app')

@section('title', 'Manage Payments')
@section('header_title', 'Payments')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar"></i></span>
            <input type="date" id="paymentDateFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " onchange="reloadPayments()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.resetForm(document.forms['createPaymentForm']);"
                data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                <i class="bi bi-plus-lg me-1"></i> Record Payment
            </button>
        @endhasrole
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Payment Modal -->
    <div class="modal fade" id="createPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createPaymentForm" action="/api/v1/payments" method="POST"
                    onsubmit="App.submitForm(event, reloadPayments, 'payment', 'createPaymentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Record Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Student</label>
                            <select name="student_id" id="payment_student_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Student</option>
                            </select>
                            <small class="text-gray-500">Target student for this payment record.</small>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Amount ($)</label>
                                <input type="number" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" step="0.01" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Date</label>
                                <input type="date" name="payment_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                        </div>
                        <div class=" mb-6  mt-4">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Payment Method</label>
                            <select name="method" id="paymentMethodSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Method</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Fee Type</label>
                            <select name="type" id="feeTypeSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Fee Type</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadPayments();
        });

        function reloadPayments() {
            const modal = document.getElementById('createPaymentModal');
            if (modal && !modal.dataset.listenerAttached) {
                modal.addEventListener('show.bs.modal', () => {
                    App.loadOptions('/api/v1/students', 'payment_student_id', null, 'id', 'full_name');
                    App.loadOptions('/api/v1/settings/enums?type=payment_method', 'paymentMethodSelect');
                    App.loadOptions('/api/v1/fee-types', 'feeTypeSelect', null, 'name',
                        'name'); // Assuming fee-types API returns {name: 'Tuition'}
                });
                modal.dataset.listenerAttached = 'true';
            }

            const dateFilter = document.getElementById('paymentDateFilter');
            const date = dateFilter ? dateFilter.value : '';

            let url = '/api/v1/payments';
            if (date) {
                url += `?date=${encodeURIComponent(date)}`;
            }
            App.renderTable(url, 'paymentsTableBody', 'payment');
        }
    </script>
@endsection
