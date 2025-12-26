@extends('layouts.app')

@section('title', 'Payment History')
@section('header_title', 'Fee Payments')

@section('content')
    <!-- Filters Toolbar -->
    <div class="card-premium mb-4">
        <div class="card-body py-3">
            <form id="paymentFilterForm" class="row g-3 align-items-end" onsubmit="event.preventDefault(); reloadPayments();">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Search Student</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="paymentSearch" class="form-control border-start-0 ps-0"
                            placeholder="Name or admission #...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Filter Student</label>
                    <select id="filterStudent" class="form-select" onchange="reloadPayments()">
                        <option value="">All Students</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Payment Method</label>
                    <select id="filterMethod" class="form-select" onchange="reloadPayments()">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="pos">POS</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-light w-100" onclick="resetFilters()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Transaction Ref</th>
                            <th>Student</th>
                            <th>Fee Item</th>
                            <th>Amount Paid</th>
                            <th>Date / Method</th>
                            <th>Processed By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody">
                        <!-- Content loaded via JS -->
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top text-center" id="paginationInfo">
                <small class="text-muted">Loading...</small>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadPayments();

            // Load Students for filter
            App.loadOptions('/api/v1/students', 'filterStudent', null, 'id', 'full_name');
        });

        function reloadPayments() {
            const studentId = document.getElementById('filterStudent').value;
            const method = document.getElementById('filterMethod').value;
            const search = document.getElementById('paymentSearch').value;

            let url = `/api/v1/fee-payments?per_page=20`;
            if (studentId) url += `&student_id=${studentId}`;
            if (method) url += `&payment_method=${method}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;

            App.renderTable(url, 'paymentsTableBody', (item) => {
                return App.safeHTML`
                    <tr>
                        <td>
                            <code class="text-primary fw-bold" style="font-size: 0.85rem;">${item.reference_number}</code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(item.student?.full_name)}&background=random" 
                                     class="avatar-xs rounded-circle me-2" width="24" alt="">
                                <div>
                                    <div class="fw-semibold small">${item.student?.full_name}</div>
                                    <div class="extra-small text-muted">${item.student?.admission_number}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small">${item.fee?.title}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-success">${App.formatCurrency(item.amount_paid)}</div>
                        </td>
                        <td>
                            <div class="small">${new Date(item.payment_date).toLocaleDateString()}</div>
                            <span class="badge bg-light text-dark extra-small border text-capitalize">${item.payment_method}</span>
                        </td>
                        <td>
                            <div class="small text-muted italic">${item.processed_by?.name || 'System'}</div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-light btn-sm shadow-sm" onclick="printReceipt('${item.id}')" title="Print Receipt">
                                <i class="bi bi-printer"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        function resetFilters() {
            document.getElementById('paymentFilterForm').reset();
            reloadPayments();
        }

        function printReceipt(id) {
            Swal.fire({
                icon: 'info',
                title: 'Receipt Generation',
                text: 'The receipt printing feature is being integrated.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        }
    </script>
@endsection
