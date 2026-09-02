@extends('layouts.app')

@section('title', 'Platform Payments')
@section('header_title', 'Revenue & Payments')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-6   mb-6 ">
        <div class=" md:col-span-4 col-span-1 ">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 p-3">
                <p class="text-gray-500 text-uppercase small  mb-6 ">Total Revenue</p>
                <h3 class="h3  mb-6 ">$0.00</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-4">
        <h5 class=" mb-6 ">Recent Transactions</h5>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle">
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
                        <td colspan="5" class=" text-center  py-4">Loading...</td>
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
                        tbody.innerHTML = '<tr><td colspan="5" class=" text-center ">No payments found.</td></tr>';
                        return;
                    }

                    payments.forEach(payment => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td data-label="Transaction ID" class="font-monospace small">${payment.id}</td>
                    <td data-label="School">${payment.school ? payment.school.name : 'Unknown'}</td>
                    <td data-label="Amount" class="font-bold">$${payment.amount}</td>
                    <td data-label="Status"><span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">${payment.status}</span></td>
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
