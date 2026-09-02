@extends('layouts.app')

@section('title', 'Reports')
@section('header_title', 'Analytics & Reports')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-6 ">
        <div class=" md:col-span-4 col-span-1 ">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 p-4  text-center  hover-up cursor-pointer">
                <div class="avatar-lg bg-soft-primary text-primary rounded-circle mx-auto  mb-6   flex   items-center   justify-center "
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-person-lines-fill fs-3"></i>
                </div>
                <h5>Student Attendance</h5>
                <p class="text-gray-500 small">Generate monthly attendance sheets and absentee reports.</p>
                <button class="btn px-3 py-1.5 text-sm btn-outline-primary mt-4">View Reports</button>
            </div>
        </div>
        <div class=" md:col-span-4 col-span-1 ">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 p-4  text-center  hover-up cursor-pointer">
                <div class="avatar-lg bg-soft-success text-success rounded-circle mx-auto  mb-6   flex   items-center   justify-center "
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-graph-up-arrow fs-3"></i>
                </div>
                <h5>Financial Overview</h5>
                <p class="text-gray-500 small">Income statements, outstanding fees, and payment history.</p>
                <button class="btn px-3 py-1.5 text-sm btn-outline-success mt-4">View Reports</button>
            </div>
        </div>
        <div class=" md:col-span-4 col-span-1 ">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 p-4  text-center  hover-up cursor-pointer">
                <div class="avatar-lg bg-soft-info text-info rounded-circle mx-auto  mb-6   flex   items-center   justify-center "
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-award fs-3"></i>
                </div>
                <h5>Academic Performance</h5>
                <p class="text-gray-500 small">Class averages, top performers, and term results.</p>
                <a href="/reports/academic" class="btn px-3 py-1.5 text-sm btn-outline-info mt-4">View Reports</a>
            </div>
        </div>
    </div>
@endsection
