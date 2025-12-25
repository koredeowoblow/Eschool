@extends('layouts.app')

@section('title', 'Manage Payments')
@section('header_title', 'Payments')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar"></i></span>
            <input type="date" id="paymentDateFilter" class="form-control border-start-0 ps-0" onchange="reloadPayments()">
        </div>

        @hasrole('super_admin|school_admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createPaymentForm']);"
                data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                <i class="bi bi-plus-lg me-1"></i> Record Payment
            </button>
        @endhasrole
    </div>

    <!-- Payments Table -->
    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th class="text-end">Actions</th>
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
                        <h5 class="modal-title fw-bold">Record Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <select name="student_id" id="payment_student_id" class="form-select" required>
                                <option value="">Select Student</option>
                            </select>
                            <small class="text-muted">Target student for this payment record.</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount ($)</label>
                                <input type="number" name="amount" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Payment Method</label>
                            <select name="method" id="paymentMethodSelect" class="form-select" required>
                                <option value="">Select Method</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fee Type</label>
                            <select name="type" id="feeTypeSelect" class="form-select" required>
                                <option value="">Select Fee Type</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Payment</button>
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
