@extends('layouts.app')

@section('title', 'Payment History')
@section('header_title', 'Fee Payments')

@section('content')
    <!-- Filters Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden  mb-6 ">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200  py-3">
            <form id="paymentFilterForm" class="row  gap-4  align-items-end" onsubmit="event.preventDefault(); reloadPayments();">
                <div class=" md:col-span-4 col-span-1 ">
                    <label class="block text-sm font-medium text-gray-700  mb-6  small font-bold">Search Student</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="paymentSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all "
                            placeholder="Name or admission #...">
                    </div>
                </div>
                <div class=" md:col-span-3 col-span-1 ">
                    <label class="block text-sm font-medium text-gray-700  mb-6  small font-bold">Filter Student</label>
                    <select id="filterStudent" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" onchange="reloadPayments()">
                        <option value="">All Students</option>
                    </select>
                </div>
                <div class=" md:col-span-3 col-span-1 ">
                    <label class="block text-sm font-medium text-gray-700  mb-6  small font-bold">Payment Method</label>
                    <select id="filterMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" onchange="reloadPayments()">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="pos">POS</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="col-md-2 text-right">
                    <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium w-100" onclick="resetFilters()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                    <thead>
                        <tr>
                            <th>Transaction Ref</th>
                            <th>Student</th>
                            <th>Fee Item</th>
                            <th>Amount Paid</th>
                            <th>Date / Method</th>
                            <th>Processed By</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody">
                        <!-- Content loaded via JS -->
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top  text-center " id="paginationInfo">
                <small class="text-gray-500">Loading...</small>
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
                            <code class="text-primary font-bold" style="font-size: 0.85rem;">${item.reference_number}</code>
                        </td>
                        <td>
                            <div class=" flex   items-center ">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(item.student?.full_name)}&background=random" 
                                     class="avatar-xs rounded-circle me-2" width="24" alt="">
                                <div>
                                    <div class="fw-semibold small">${item.student?.full_name}</div>
                                    <div class="extra-small text-gray-500">${item.student?.admission_number}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small">${item.fee?.title}</div>
                        </td>
                        <td>
                            <div class="font-bold text-success">${App.formatCurrency(item.amount_paid)}</div>
                        </td>
                        <td>
                            <div class="small">${new Date(item.payment_date).toLocaleDateString()}</div>
                            <span class="badge bg-light text-dark extra-small border text-capitalize">${item.payment_method}</span>
                        </td>
                        <td>
                            <div class="small text-gray-500 italic">${item.processed_by?.name || 'System'}</div>
                        </td>
                        <td class="text-right">
                            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium px-3 py-1.5 text-sm shadow-sm" onclick="printReceipt('${item.id}')" title="Print Receipt">
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
