@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Overview')

@section('content')
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
@endsection

@section('scripts')
    <script>
        // Dashboard Logic is handled by premium-app.js
        // ensuring roles get the correct data view
    </script>
@endsection
