@extends('layouts.app')

@section('title', 'Platform Payments')
@section('header_title', 'Revenue & Payments')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card-premium h-100 p-3">
                <p class="text-muted text-uppercase small mb-1">Total Revenue</p>
                <h3 class="h3 mb-0">$0.00</h3>
            </div>
        </div>
    </div>

    <div class="card-premium p-4">
        <h5 class="mb-4">Recent Transactions</h5>
        <div class="table-responsive">
            <table class="table table-premium table-hover align-middle">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>School</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="payments-table-body">
                    <tr>
                        <td colspan="5" class="text-center py-4">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadPayments();
        });

        function loadPayments() {
            axios.get("/api/v1/payments", {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    const payments = response.data.data.data;
                    const tbody = document.getElementById('payments-table-body');
                    tbody.innerHTML = '';

                    if (!payments || payments.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No payments found.</td></tr>';
                        return;
                    }

                    payments.forEach(payment => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td data-label="Transaction ID" class="font-monospace small">${payment.id}</td>
                    <td data-label="School">${payment.school ? payment.school.name : 'Unknown'}</td>
                    <td data-label="Amount" class="fw-bold">$${payment.amount}</td>
                    <td data-label="Status"><span class="badge bg-success-subtle text-success">${payment.status}</span></td>
                    <td data-label="Date">${new Date(payment.created_at).toLocaleDateString()}</td>
                `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(e => {
                    document.getElementById('payments-table-body').innerHTML =
                        '<tr><td colspan="5">Error loading payments</td></tr>';
                });
        }
    </script>
@endsection
