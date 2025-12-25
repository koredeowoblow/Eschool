@extends('layouts.app')

@section('title', 'Manage Invoices')
@section('header_title', 'Invoices')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="invoiceSearch" class="form-control border-start-0 ps-0" placeholder="Search invoices..."
                oninput="reloadInvoices()">
        </div>

        @hasrole('super_admin|school_admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createInvoiceForm']);"
                data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                <i class="bi bi-plus-lg me-1"></i> Create Invoice
            </button>
        @endhasrole
    </div>

    <!-- Filters Bar -->
    <div class="card-premium mb-4 bg-light border-0">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select form-select-sm" onchange="reloadInvoices()">
                        <option value="">All Statuses</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="sessionFilter" class="form-select form-select-sm" onchange="reloadInvoices()">
                        <option value="">All Sessions</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="termFilter" class="form-select form-select-sm" onchange="reloadInvoices()">
                        <option value="">All Terms</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-sm btn-link text-decoration-none" onclick="resetFilters()">Reset Filters</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th class="sortable-header" data-sort="invoice_number">Invoice #</th>
                            <th class="sortable-header" data-sort="student_id">Student</th>
                            <th class="sortable-header" data-sort="amount">Total Amount</th>
                            <th>Paid</th>
                            <th class="sortable-header" data-sort="due_date">Due Date</th>
                            <th class="sortable-header" data-sort="status">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Invoice Modal -->
    <div class="modal fade" id="createInvoiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createInvoiceForm" action="/api/v1/invoices" method="POST"
                    onsubmit="App.submitForm(event, reloadInvoices, 'invoice', 'createInvoiceModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create New Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Student *</label>
                                <select name="student_id" id="invoice_student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date *</label>
                                <input type="date" name="due_date" class="form-control"
                                    value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Session *</label>
                                <select name="session_id" id="invoice_session_id" class="form-select" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Term *</label>
                                <select name="term_id" id="invoice_term_id" class="form-select" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 d-flex justify-content-between">
                            Invoice Items
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInvoiceItem()">
                                <i class="bi bi-plus"></i> Add Item
                            </button>
                        </h6>
                        <div id="invoiceItemsContainer" class="vstack gap-2 mb-3">
                            <!-- Items dynamically added here -->
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Internal notes or memo"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Generate Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadInvoices();

            // Initial load of global filters
            App.loadOptions('/api/v1/sessions', 'sessionFilter');
            App.loadOptions('/api/v1/terms', 'termFilter');

            const createModal = document.getElementById('createInvoiceModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/students', 'invoice_student_id', 'id', 'full_name',
                    'Select Student');
                App.loadOptions('/api/v1/sessions', 'invoice_session_id');
                App.loadOptions('/api/v1/terms', 'invoice_term_id');
                // Start with one empty item
                const container = document.getElementById('invoiceItemsContainer');
                container.innerHTML = '';
                addInvoiceItem();
            });
        });

        function addInvoiceItem() {
            const container = document.getElementById('invoiceItemsContainer');
            const rowCount = container.children.length;
            const div = document.createElement('div');
            div.className = 'row g-2 align-items-center border-bottom pb-2 item-row';
            div.innerHTML = `
                <div class="col-md-6">
                    <input type="text" name="items[${rowCount}][name]" class="form-control form-control-sm" placeholder="Item Name (e.g. Tuition Fee)" required>
                </div>
                <div class="col-md-4">
                    <input type="number" name="items[${rowCount}][amount]" class="form-control form-control-sm" placeholder="Amount" step="0.01" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-sm btn-light text-danger" onclick="this.closest('.item-row').remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        function reloadInvoices() {
            const query = document.getElementById('invoiceSearch').value;
            const status = document.getElementById('statusFilter').value;
            const session = document.getElementById('sessionFilter').value;
            const term = document.getElementById('termFilter').value;
            const studentId = window.App?.currentStudentId || '';

            let url = `/api/v1/invoices?search=${encodeURIComponent(query)}&student_id=${studentId}`;
            if (status) url += `&status=${status}`;
            if (session) url += `&session_id=${session}`;
            if (term) url += `&term_id=${term}`;

            App.renderTable(url, 'invoicesTableBody', (item) => {
                const statusInfo = {
                    'paid': {
                        color: 'success'
                    },
                    'partial': {
                        color: 'warning'
                    },
                    'unpaid': {
                        color: 'danger'
                    },
                    'overdue': {
                        color: 'dark'
                    }
                } [item.status] || {
                    color: 'secondary'
                };

                const total = parseFloat(item.total_amount || 0);
                const paid = parseFloat(item.paid_amount || 0);
                const balance = total - paid;

                return App.safeHTML`
                    <tr>
                        <td><code class="text-primary fw-bold">#${item.invoice_number}</code></td>
                        <td>
                            <div class="fw-bold text-dark">${item.student?.full_name}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">${item.student?.admission_number}</small>
                        </td>
                        <td class="fw-bold text-dark">$${total.toFixed(2)}</td>
                        <td class="text-success fw-medium">$${paid.toFixed(2)}</td>
                        <td>
                            <div class="${new Date(item.due_date) < new Date() && item.status !== 'paid' ? 'text-danger fw-bold' : 'text-muted sm'}" style="font-size: 0.85rem;">
                                ${new Date(item.due_date).toLocaleDateString()}
                            </div>
                        </td>
                        <td><span class="badge rounded-pill bg-${statusInfo.color}-subtle text-${statusInfo.color} px-3 text-capitalize">${item.status}</span></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                ${balance > 0 ? `
                                                <button class="btn btn-primary-premium btn-sm py-1 shadow-sm" onclick="payInvoice(${item.id}, ${balance})" title="Pay Now">
                                                    <i class="bi bi-credit-card me-1"></i> Pay
                                                </button>
                                            ` : ''}
                                <button class="btn btn-light shadow-sm btn-sm" onclick="App.deleteItem('/api/v1/invoices/${item.id}', reloadInvoices)" title="Delete">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        function payInvoice(id, amount) {
            // In a real app, this might open a payment gateway or pre-fill the Payment Record modal
            window.location.href = `/payments?invoice_id=${id}&amount=${amount}`;
        }

        function resetFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('sessionFilter').value = '';
            document.getElementById('termFilter').value = '';
            document.getElementById('invoiceSearch').value = '';
            reloadInvoices();
        }
    </script>
@endsection
