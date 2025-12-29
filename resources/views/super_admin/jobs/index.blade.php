@extends('layouts.app')

@section('title', 'System Jobs')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                <h4 class="mb-0 fw-bold text-gradient">System Jobs</h4>
                <button class="btn btn-primary-premium" onclick="retryAllJobs()">
                    <i class="bi bi-arrow-clockwise me-2"></i> Retry All
                </button>
            </div>

            <div class="card-premium">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Connection</th>
                                    <th>Queue</th>
                                    <th>Failed At</th>
                                    <th>Exception</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="jobsTableBody">
                                <!-- Content loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top text-center">
                        <small class="text-muted">Displaying recent failed jobs.</small>
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
                        <td><span class="fw-bold text-dark">#${job.id}</span></td>
                        <td><span class="badge bg-secondary-subtle text-secondary">${job.connection}</span></td>
                        <td><span class="badge bg-info-subtle text-info">${job.queue}</span></td>
                        <td><small class="text-muted">${failedAt}</small></td>
                        <td title="${job.exception}">
                            <div class="text-truncate" style="max-width: 300px;">
                                <small class="text-danger">${exceptionShort}</small>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-light shadow-sm btn-sm"
                                    onclick="retryJob(${job.id})" title="Retry">
                                    <i class="bi bi-arrow-repeat text-primary"></i>
                                </button>
                                <button class="btn btn-light shadow-sm btn-sm"
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
