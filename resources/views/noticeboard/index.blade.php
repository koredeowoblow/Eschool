@extends('layouts.app')

@section('title', 'Noticeboard')
@section('header_title', 'School Noticeboard')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="noticeSearch" class="form-control border-start-0 ps-0" placeholder="Search notices..."
                oninput="reloadNotices()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="btn btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createNoticeModal">
                <i class="bi bi-plus-lg me-1"></i> Post Announcement
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Urgency</th>
                            <th>Posted By</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="noticeTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createNoticeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createNoticeForm" action="/api/v1/noticeboard" method="POST"
                    onsubmit="App.submitForm(event, reloadNotices, 'notice', 'createNoticeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Post Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title / Subject</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Resumption Date"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Urgency Level</label>
                            <select name="type" class="form-select">
                                <option value="Info">General Information</option>
                                <option value="Urgent">Urgent / Important</option>
                                <option value="Event">Event Notification</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Announcement Content</label>
                            <textarea name="content" class="form-control" rows="5" placeholder="Write full details..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Publish Notice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/noticeboard', 'noticeTableBody', 'notice');
        });

        function reloadNotices() {
            const query = document.getElementById('noticeSearch').value;
            App.renderTable('/api/v1/noticeboard?search=' + encodeURIComponent(query), 'noticeTableBody', 'notice');
        }
    </script>
@endsection
