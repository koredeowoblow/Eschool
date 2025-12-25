@extends('layouts.app')

@section('title', 'Assignment Submissions')
@section('header_title', 'Assignment Submissions')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="submissionSearch" class="form-control border-start-0 ps-0" placeholder="Search submissions..." oninput="reloadSubmissions()">
        </div>
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Student</th>
                            <th>Submitted At</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th class="text-end">Actions</th>
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
