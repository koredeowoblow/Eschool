@extends('layouts.app')

@section('title', 'Student Fee Overview')
@section('header_title', 'Fee Ledger')

@section('content')
    <!-- Student Header -->
    <div class="card-premium mb-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-4 bg-primary-subtle border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <img id="studentAvatar" src="https://ui-avatars.com/api/?name=Student&background=random"
                        class="rounded-circle border border-3 border-white shadow-sm" width="80" height="80"
                        alt="">
                    <div>
                        <h4 class="fw-bold mb-1" id="studentName">Loading...</h4>
                        <div class="d-flex gap-3 small">
                            <span><i class="bi bi-person-badge me-1"></i> <span id="studentAdmission">...</span></span>
                            <span><i class="bi bi-building me-1"></i> <span id="studentClass">...</span></span>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill shadow-sm"
                        id="studentStatusBadge">Active</span>
                </div>
            </div>
            <div class="row g-0 text-center border-bottom">
                <div class="col-md-4 p-4 border-end">
                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Total Fees</small>
                    <h2 class="fw-bold mb-0" id="statTotalFees">$0.00</h2>
                </div>
                <div class="col-md-4 p-4 border-end">
                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Total Paid</small>
                    <h2 class="fw-bold text-success mb-0" id="statTotalPaid">$0.00</h2>
                </div>
                <div class="col-md-4 p-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-1">Outstanding Balance</small>
                    <h2 class="fw-bold text-danger mb-0" id="statBalance">$0.00</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Items -->
    <div class="card-premium">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Assigned Fee Items</h5>
            <button class="btn btn-sm btn-outline-primary" onclick="syncFees()">
                <i class="bi bi-arrow-repeat me-1"></i> Sync Mandatory Fees
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fee Title</th>
                            <th>Term / Session</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
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
                        <h5 class="modal-title fw-bold">Process Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="vstack gap-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="small text-muted mb-1">Fee Item</label>
                                <div class="fw-bold" id="paymentFeeTitle">...</div>
                                <div class="row mt-2 small">
                                    <div class="col-6">Amount: <span id="paymentTotalAmount">$0.00</span></div>
                                    <div class="col-6 text-danger fw-bold">Balance: <span id="paymentBalance">$0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Payment Amount *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="amount_paid" id="paymentAmountInput"
                                        class="form-control form-control-lg fw-bold text-success" step="0.01" required>
                                </div>
                                <small class="text-muted mt-1 d-block" id="fullPaymentHint">Click to pay balance</small>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Payment Method *</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="pos">POS</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Reference (Optional)</label>
                                    <input type="text" name="reference_number" class="form-control"
                                        placeholder="TXN-123...">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium px-4">Confirm Payment</button>
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
                        '<tr><td colspan="7" class="text-center py-4 text-muted">No fees assigned to this student.</td></tr>';
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
                            <div class="fw-bold">${fee.title}</div>
                            <small class="text-muted uppercase small">${fee.fee_type}</small>
                        </td>
                        <td>
                            <div class="small">${fee.term?.name || 'N/A'}</div>
                            <div class="extra-small text-muted">${fee.session?.name || 'N/A'}</div>
                        </td>
                        <td>${App.formatCurrency(fee.amount)}</td>
                        <td class="text-success">${App.formatCurrency(parseFloat(fee.amount) - parseFloat(item.balance))}</td>
                        <td class="fw-bold ${parseFloat(item.balance) > 0 ? 'text-danger' : 'text-success'}">${App.formatCurrency(item.balance)}</td>
                        <td>
                            <span class="badge bg-${statusClass}-subtle text-${statusClass} text-capitalize px-3">${item.status}</span>
                        </td>
                        <td class="text-end">
                            ${parseFloat(item.balance) > 0 ? `
                                    <button class="btn btn-primary-premium btn-sm" onclick="openPaymentModal('${fee.id}', '${fee.title.replace(/'/g, "\\'")}', ${fee.amount}, ${item.balance})">
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
