@extends('layouts.app')

@section('title', 'Noticeboard')
@section('header_title', 'School Noticeboard')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="noticeSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search notices..."
                oninput="reloadNotices()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-toggle="modal" data-bs-target="#createNoticeModal">
                <i class="bi bi-plus-lg me-1"></i> Post Announcement
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Urgency</th>
                            <th>Posted By</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
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
                        <h5 class="modal-title font-bold">Post Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Title / Subject</label>
                            <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="e.g. Resumption Date"
                                required>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Urgency Level</label>
                            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                <option value="Info">General Information</option>
                                <option value="Urgent">Urgent / Important</option>
                                <option value="Event">Event Notification</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Announcement Content</label>
                            <textarea name="content" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="5" placeholder="Write full details..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Publish Notice</button>
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
