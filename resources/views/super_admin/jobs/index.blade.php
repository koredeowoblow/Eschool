@extends('layouts.app')

@section('title', 'System Jobs')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class=" col-span-1 md:col-span-12 ">
            <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
                <h4 class=" mb-6  font-bold text-gradient">System Jobs</h4>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="retryAllJobs()">
                    <i class="bi bi-arrow-clockwise me-2"></i> Retry All
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Connection</th>
                                    <th>Queue</th>
                                    <th>Failed At</th>
                                    <th>Exception</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="jobsTableBody">
                                <!-- Content loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top  text-center ">
                        <small class="text-gray-500">Displaying recent failed jobs.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            reloadJobs();
        });

        function reloadJobs() {
            App.renderTable('/api/v1/jobs', 'jobsTableBody', (job) => {
                const exceptionShort = job.exception.substring(0, 100) + '...';
                const failedAt = new Date(job.failed_at).toLocaleString();

                return App.safeHTML`
                    <tr>
                        <td><span class="font-bold text-dark">#${job.id}</span></td>
                        <td><span class=" px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 -subtle text-secondary">${job.connection}</span></td>
                        <td><span class=" px-2.5 py-1 text-xs font-semibold rounded-md bg-blue-100 text-blue-700 -subtle text-info">${job.queue}</span></td>
                        <td><small class="text-gray-500">${failedAt}</small></td>
                        <td title="${job.exception}">
                            <div class="text-truncate" style="max-width: 300px;">
                                <small class="text-danger">${exceptionShort}</small>
                            </div>
                        </td>
                        <td class="text-right">
                            <div class=" flex   justify-end  gap-2">
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm"
                                    onclick="retryJob(${job.id})" title="Retry">
                                    <i class="bi bi-arrow-repeat text-primary"></i>
                                </button>
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm"
                                    onclick="deleteJob(${job.id})" title="Delete">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        async function retryJob(id) {
            if (!confirm('Are you sure you want to retry this job?')) return;
            try {
                // Laravel route requires a parameter. We use 0 as placeholder.
                const url = `/api/v1/jobs/retry/${id}`;
                await axios.post(url);
                toastr.success('Job queued for retry');
                reloadJobs();
            } catch (error) {
                console.error('Error retrying job:', error);
                toastr.error('Failed to retry job');
            }
        }

        async function deleteJob(id) {
            if (!confirm('Are you sure you want to delete this job?')) return;
            try {
                const url = `/api/v1/jobs/${id}`;
                await axios.delete(url);
                toastr.success('Job deleted successfully');
                reloadJobs();
            } catch (error) {
                console.error('Error deleting job:', error);
                toastr.error('Failed to delete job');
            }
        }

        async function retryAllJobs() {
            if (!confirm('Are you sure you want to retry ALL failed jobs?')) return;
            try {
                await axios.post(`/api/v1/jobs/retry/all`);
                toastr.success('All jobs queued for retry');
                reloadJobs();
            } catch (error) {
                console.error('Error retrying all jobs:', error);
                toastr.error('Failed to retry all jobs');
            }
        }
    </script>
@endsection
