@extends('layouts.app')

@section('title', 'Student Fee Overview')
@section('header_title', 'Fee Ledger')

@section('content')
    <!-- Student Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden  mb-6  overflow-hidden">
        <div class="p-0">
            <div class="p-4 bg-primary-subtle border-bottom  flex   items-center   justify-between ">
                <div class="flex items-center gap-3">
                    <img id="studentAvatar" src="https://ui-avatars.com/api/?name=Student&background=random"
                        class="rounded-circle border border-3 border-white shadow-sm" width="80" height="80"
                        alt="">
                    <div>
                        <h4 class="font-bold  mb-6 " id="studentName">Loading...</h4>
                        <div class=" flex  gap-3 small">
                            <span><i class="bi bi-person-badge me-1"></i> <span id="studentAdmission">...</span></span>
                            <span><i class="bi bi-building me-1"></i> <span id="studentClass">...</span></span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 px-3 py-2 rounded-pill shadow-sm"
                        id="studentStatusBadge">Active</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 g-0  text-center  border-bottom">
                <div class=" md:col-span-4 col-span-1  p-4 border-end">
                    <small class="text-gray-500 text-uppercase font-bold d-block  mb-6 ">Total Fees</small>
                    <h2 class="font-bold  mb-6 " id="statTotalFees">$0.00</h2>
                </div>
                <div class=" md:col-span-4 col-span-1  p-4 border-end">
                    <small class="text-gray-500 text-uppercase font-bold d-block  mb-6 ">Total Paid</small>
                    <h2 class="font-bold text-success  mb-6 " id="statTotalPaid">$0.00</h2>
                </div>
                <div class=" md:col-span-4 col-span-1  p-4">
                    <small class="text-gray-500 text-uppercase font-bold d-block  mb-6 ">Outstanding Balance</small>
                    <h2 class="font-bold text-danger  mb-6 " id="statBalance">$0.00</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Items -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header bg-white py-3 flex justify-between items-center">
            <h5 class="font-bold  mb-6 "><i class="bi bi-receipt me-2"></i>Assigned Fee Items</h5>
            <button class="btn px-3 py-1.5 text-sm btn-outline-primary" onclick="syncFees()">
                <i class="bi bi-arrow-repeat me-1"></i> Sync Mandatory Fees
            </button>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                    <thead>
                        <tr>
                            <th>Fee Title</th>
                            <th>Term / Session</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentFeesTableBody">
                        <!-- Content loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="paymentForm" action="/api/v1/fee-payments" method="POST"
                    onsubmit="App.submitForm(event, onPaymentSuccess, 'feePayment', 'paymentModal')">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student_id }}">
                    <input type="hidden" name="fee_id" id="paymentFeeId">

                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Process Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="vstack gap-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-gray-500  mb-6 ">Fee Item</label>
                                <div class="font-bold" id="paymentFeeTitle">...</div>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-4 small">
                                    <div class="col-6">Amount: <span id="paymentTotalAmount">$0.00</span></div>
                                    <div class="col-6 text-danger font-bold">Balance: <span id="paymentBalance">$0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Payment Amount *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="amount_paid" id="paymentAmountInput"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all-lg font-bold text-success" step="0.01" required>
                                </div>
                                <small class="text-gray-500 mt-4 d-block" id="fullPaymentHint">Click to pay balance</small>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Payment Method *</label>
                                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="pos">POS</option>
                                    </select>
                                </div>
                                <div class=" md:col-span-6 col-span-1 ">
                                    <label class="block text-sm font-medium text-gray-700  mb-6 ">Reference (Optional)</label>
                                    <input type="text" name="reference_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="TXN-123...">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Payment Date</label>
                                <input type="date" name="payment_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium px-4">Confirm Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const studentId = "{{ $student_id }}";

        document.addEventListener('DOMContentLoaded', () => {
            loadStudentData();
            loadStudentFees();
        });

        async function loadStudentData() {
            try {
                const res = await axios.get(`/api/v1/students/${studentId}`);
                const student = res.data.data;

                document.getElementById('studentName').textContent = student.full_name;
                document.getElementById('studentAdmission').textContent = student.admission_number;
                document.getElementById('studentClass').textContent = student.current_class || 'N/A';
                document.getElementById('studentAvatar').src =
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(student.full_name)}&background=2563eb&color=fff`;

                const statusBadge = document.getElementById('studentStatusBadge');
                const isActive = (student.status === 'active' || student.status === 1 || student.status === true);
                statusBadge.textContent = isActive ? 'Active' : 'Inactive';
                statusBadge.className =
                    `badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-3 py-2 rounded-pill shadow-sm`;
            } catch (err) {
                console.error('Failed to load student data', err);
            }
        }

        async function loadStudentFees() {
            try {
                const res = await axios.get(`/api/v1/students/${studentId}/outstanding-fees`);
                const fees = res.data.data;
                const tbody = document.getElementById('studentFeesTableBody');

                tbody.replaceChildren();

                let totalFees = 0;
                let totalPaid = 0;
                let balance = 0;

                if (fees.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="7" class=" text-center  py-4 text-gray-500">No fees assigned to this student.</td></tr>';
                }

                fees.forEach(item => {
                    const fee = item.fee;
                    totalFees += parseFloat(fee.amount);
                    totalPaid += (parseFloat(fee.amount) - parseFloat(item.balance));
                    balance += parseFloat(item.balance);

                    const statusClass = getStatusClass(item.status);

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <div class="font-bold">${fee.title}</div>
                            <small class="text-gray-500 uppercase small">${fee.fee_type}</small>
                        </td>
                        <td>
                            <div class="small">${fee.term?.name || 'N/A'}</div>
                            <div class="extra-small text-gray-500">${fee.session?.name || 'N/A'}</div>
                        </td>
                        <td>${App.formatCurrency(fee.amount)}</td>
                        <td class="text-success">${App.formatCurrency(parseFloat(fee.amount) - parseFloat(item.balance))}</td>
                        <td class="font-bold ${parseFloat(item.balance) > 0 ? 'text-danger' : 'text-success'}">${App.formatCurrency(item.balance)}</td>
                        <td>
                            <span class="badge bg-${statusClass}-subtle text-${statusClass} text-capitalize px-3">${item.status}</span>
                        </td>
                        <td class="text-right">
                            ${parseFloat(item.balance) > 0 ? `
                                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium px-3 py-1.5 text-sm" onclick="openPaymentModal('${fee.id}', '${fee.title.replace(/'/g, "\\'")}', ${fee.amount}, ${item.balance})">
                                        <i class="bi bi-wallet2 me-1"></i> Pay
                                    </button>
                                ` : `
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                `}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                document.getElementById('statTotalFees').textContent = App.formatCurrency(totalFees);
                document.getElementById('statTotalPaid').textContent = App.formatCurrency(totalPaid);
                document.getElementById('statBalance').textContent = App.formatCurrency(balance);

            } catch (err) {
                console.error('Failed to load fees', err);
            }
        }

        function getStatusClass(status) {
            switch (status) {
                case 'paid':
                    return 'success';
                case 'partial':
                    return 'warning';
                case 'pending':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        function openPaymentModal(feeId, title, total, balance) {
            document.getElementById('paymentFeeId').value = feeId;
            document.getElementById('paymentFeeTitle').textContent = title;
            document.getElementById('paymentTotalAmount').textContent = App.formatCurrency(total);
            document.getElementById('paymentBalance').textContent = App.formatCurrency(balance);
            document.getElementById('paymentAmountInput').value = balance;

            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }

        function onPaymentSuccess() {
            loadStudentFees();
        }

        async function syncFees() {
            try {
                Swal.fire({
                    title: 'Syncing Fees...',
                    didOpen: () => Swal.showLoading()
                });
                await axios.post(`/api/v1/students/${studentId}/sync-fees`);
                Swal.close();
                loadStudentFees();
                Swal.fire({
                    icon: 'success',
                    title: 'Synced',
                    text: 'Mandatory fees have been updated.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sync Failed',
                    text: err.response?.data?.message || 'Something went wrong.'
                });
            }
        }
    </script>
@endsection
