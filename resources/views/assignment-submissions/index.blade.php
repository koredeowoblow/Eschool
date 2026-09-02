@extends('layouts.app')

@section('title', 'Assignment Submissions')
@section('header_title', 'Assignment Submissions')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="submissionSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search submissions..." oninput="reloadSubmissions()">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Student</th>
                            <th>Submitted At</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="submissionsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
		reloadSubmissions();
    });

    function reloadSubmissions() {
        const query = document.getElementById('submissionSearch').value;
        App.renderTable('/api/v1/assignment-submissions?search=' + encodeURIComponent(query), 'submissionsTableBody', 'assignmentSubmission');
    }

    function deleteSubmission(id) {
		App.deleteItem(`/api/v1/assignment-submissions/${id}`, reloadSubmissions);
    }
</script>
@endsection
