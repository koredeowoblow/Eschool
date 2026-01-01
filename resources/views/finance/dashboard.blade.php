@extends('layouts.app')

@section('title', 'Finance Dashboard')
@section('header_title', 'Finance Dashboard')

@section('content')
    <div class="row" id="finance-overview">
        <!-- Overview Cards -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Total Revenue</h6>
                    <h3 class="fw-bold text-primary" id="total-invoiced">
                        <span class="spinner-border spinner-border-sm"></span>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Outstanding</h6>
                    <h3 class="fw-bold text-success" id="total-collected">
                        <span class="spinner-border spinner-border-sm"></span>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-2">Pending Invoices</h6>
                    <h3 class="fw-bold text-warning" id="pending">
                        <span class="spinner-border spinner-border-sm"></span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div
                    class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <h5 class="mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    @can('finance.generate.invoices')
                        <button class="btn btn-primary me-2" onclick="alert('Open Create Invoice Modal')">
                            <i class="bi bi-plus-lg me-1"></i> Create Invoice
                        </button>
                    @endcan

                    @can('finance.record.payments')
                        <button class="btn btn-success me-2" onclick="alert('Open Record Payment Modal')">
                            <i class="bi bi-cash me-1"></i> Record Payment
                        </button>
                    @endcan

                    @can('finance.export.reports')
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-download me-1"></i> Export Report
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await axios.get('/api/v1/finance/overview');
                const data = response.data.data;

                document.getElementById('total-invoiced').textContent =
                    '$' + parseFloat(data.total_revenue || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                document.getElementById('total-collected').textContent =
                    '$' + parseFloat(data.total_outstanding || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                document.getElementById('pending').textContent = data.pending_invoices_count || 0;

            } catch (error) {
                console.error('Failed to load finance data:', error);
                ['total-invoiced', 'total-collected', 'pending'].forEach(id => {
                    document.getElementById(id).innerHTML = '<span class="text-danger">Error</span>';
                });
            }
        });
    </script>
@endsection
