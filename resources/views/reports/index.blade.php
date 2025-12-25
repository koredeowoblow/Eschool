@extends('layouts.app')

@section('title', 'Reports')
@section('header_title', 'Analytics & Reports')

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium h-100 p-4 text-center hover-up cursor-pointer">
                <div class="avatar-lg bg-soft-primary text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-person-lines-fill fs-3"></i>
                </div>
                <h5>Student Attendance</h5>
                <p class="text-muted small">Generate monthly attendance sheets and absentee reports.</p>
                <button class="btn btn-sm btn-outline-primary mt-2">View Reports</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium h-100 p-4 text-center hover-up cursor-pointer">
                <div class="avatar-lg bg-soft-success text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-graph-up-arrow fs-3"></i>
                </div>
                <h5>Financial Overview</h5>
                <p class="text-muted small">Income statements, outstanding fees, and payment history.</p>
                <button class="btn btn-sm btn-outline-success mt-2">View Reports</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium h-100 p-4 text-center hover-up cursor-pointer">
                <div class="avatar-lg bg-soft-info text-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-award fs-3"></i>
                </div>
                <h5>Academic Performance</h5>
                <p class="text-muted small">Class averages, top performers, and term results.</p>
                <a href="/reports/academic" class="btn btn-sm btn-outline-info mt-2">View Reports</a>
            </div>
        </div>
    </div>
@endsection
