@extends('layouts.app')

@section('title', 'Manage Invoices')
@section('header_title', 'Invoices')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="invoiceSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search invoices..."
                oninput="reloadInvoices()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.resetForm(document.forms['createInvoiceForm']);"
                data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                <i class="bi bi-plus-lg me-1"></i> Create Invoice
            </button>
        @endhasrole
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden  mb-6  bg-light border-0">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200  py-2">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 g-2  items-center ">
                <div class=" md:col-span-3 col-span-1 ">
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white-sm" onchange="reloadInvoices()">
                        <option value="">All Statuses</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                <div class=" md:col-span-3 col-span-1 ">
                    <select id="sessionFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white-sm" onchange="reloadInvoices()">
                        <option value="">All Sessions</option>
                    </select>
                </div>
                <div class=" md:col-span-3 col-span-1 ">
                    <select id="termFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white-sm" onchange="reloadInvoices()">
                        <option value="">All Terms</option>
                    </select>
                </div>
                <div class=" md:col-span-3 col-span-1  text-right">
                    <button class="btn px-3 py-1.5 text-sm btn-link text-decoration-none" onclick="resetFilters()">Reset Filters</button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="sortable-header" data-sort="invoice_number">Invoice #</th>
                            <th class="sortable-header" data-sort="student_id">Student</th>
                            <th class="sortable-header" data-sort="amount">Total Amount</th>
                            <th>Paid</th>
                            <th class="sortable-header" data-sort="due_date">Due Date</th>
                            <th class="sortable-header" data-sort="status">Status</th>
                            <th class="text-right">Actions</th>
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
                        <h5 class="modal-title font-bold">Create New Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Student *</label>
                                <select name="student_id" id="invoice_student_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Due Date *</label>
                                <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Session *</label>
                                <select name="session_id" id="invoice_session_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Term *</label>
                                <select name="term_id" id="invoice_term_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                        </div>

                        <h6 class="font-bold  mb-6   flex   justify-between ">
                            Invoice Items
                            <button type="button" class="btn px-3 py-1.5 text-sm btn-outline-primary" onclick="addInvoiceItem()">
                                <i class="bi bi-plus"></i> Add Item
                            </button>
                        </h6>
                        <div id="invoiceItemsContainer" class="vstack gap-2  mb-6 ">
                            <!-- Items dynamically added here -->
                        </div>

                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Notes (Optional)</label>
                            <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="2" placeholder="Internal notes or memo"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Generate Invoice</button>
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
                <div class=" md:col-span-6 col-span-1 ">
                    <input type="text" name="items[${rowCount}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all-sm" placeholder="Item Name (e.g. Tuition Fee)" required>
                </div>
                <div class=" md:col-span-4 col-span-1 ">
                    <input type="number" name="items[${rowCount}][amount]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all-sm" placeholder="Amount" step="0.01" required>
                </div>
                <div class="col-md-2 text-right">
                    <button type="button" class="btn px-3 py-1.5 text-sm btn-light text-danger" onclick="this.closest('.item-row').remove()">
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
                        <td><code class="text-primary font-bold">#${item.invoice_number}</code></td>
                        <td>
                            <div class="font-bold text-dark">${item.student?.full_name}</div>
                            <small class="text-gray-500" style="font-size: 0.7rem;">${item.student?.admission_number}</small>
                        </td>
                        <td class="font-bold text-dark">$${total.toFixed(2)}</td>
                        <td class="text-success fw-medium">$${paid.toFixed(2)}</td>
                        <td>
                            <div class="${new Date(item.due_date) < new Date() && item.status !== 'paid' ? 'text-danger font-bold' : 'text-gray-500 sm'}" style="font-size: 0.85rem;">
                                ${new Date(item.due_date).toLocaleDateString()}
                            </div>
                        </td>
                        <td><span class="badge rounded-pill bg-${statusInfo.color}-subtle text-${statusInfo.color} px-3 text-capitalize">${item.status}</span></td>
                        <td class="text-right">
                            <div class=" flex   justify-end  gap-2">
                                ${balance > 0 ? `
                                                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium px-3 py-1.5 text-sm py-1 shadow-sm" onclick="payInvoice(${item.id}, ${balance})" title="Pay Now">
                                                        <i class="bi bi-credit-bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden me-1"></i> Pay
                                                    </button>
                                                ` : ''}
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm" onclick="App.deleteItem('/api/v1/invoices/${item.id}', reloadInvoices)" title="Delete">
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
