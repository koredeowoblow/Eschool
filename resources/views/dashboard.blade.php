@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Overview')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h2 class="h4 fw-bold text-dark mb-0">Dashboard Overview</h2>
        <button class="btn btn-primary-premium w-100 w-md-auto d-flex align-items-center justify-content-center gap-2"
            data-bs-toggle="modal" data-bs-target="#linkAccountModal">
            <i class="bi bi-link-45deg fs-5"></i>
            <span>Link Account</span>
        </button>
    </div>

    <!-- Dynamic Dashboard Root -->
    <div id="dashboard-stats-root" class="row g-4">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-2">Loading dashboard...</p>
        </div>
    </div>

    <!-- Charts Container -->
    <div id="dashboard-charts-root" class="row g-4 mt-2"></div>

    <!-- Recent Activity Container -->
    <div id="dashboard-activity-root" class="mt-4"></div>

    <!-- Account Linking Modal -->
    <div class="modal fade" id="linkAccountModal" tabindex="-1" aria-labelledby="linkAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title" id="linkAccountModalLabel">Link Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Step 1: Initiate -->
                    <div id="link-step-1">
                        <p class="text-muted mb-4">Enter the email address of the account you want to link. A verification
                            code will be sent to them.</p>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" id="link-email" class="form-control" placeholder="user@example.com">
                        </div>
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold" onclick="App.initiateLinking()">
                            Send Verification Code
                        </button>
                    </div>

                    <!-- Step 2: Verify -->
                    <div id="link-step-2" class="d-none">
                        <p class="text-muted mb-4">A 6-digit code has been sent to <span id="display-link-email"
                                class="fw-bold text-dark"></span>. Please enter it below.</p>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Verification Code</label>
                            <input type="text" id="link-otp"
                                class="form-control text-center fs-4 fw-bold letter-spacing-lg" maxlength="6"
                                placeholder="000000">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary w-50 py-2 fw-bold"
                                onclick="App.switchLinkStep(1)">
                                Back
                            </button>
                            <button type="button" class="btn btn-primary w-50 py-2 fw-bold" onclick="App.verifyLinking()">
                                Verify & Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Dashboard Logic is handled by premium-app.js
        // ensuring roles get the correct data view
    </script>
@endsection
