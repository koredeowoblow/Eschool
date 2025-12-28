@extends('layouts.app')

@section('title', 'System Jobs')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Failed Jobs</h4>
                    <button class="btn btn-primary" onclick="retryAllJobs()">
                        <i class="fas fa-redo me-2"></i> Retry All
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="jobs-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Connection</th>
                                    <th>Queue</th>
                                    <th>Failed At</th>
                                    <th>Exception</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchJobs();
        });

        async function fetchJobs(page = 1) {
            try {
                const response = await axios.get(`{{ route('api.jobs.index') }}?page=${page}`);
                const {
                    data,
                    meta
                } = response.data.data;

                const tbody = document.querySelector('#jobs-table tbody');
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No failed jobs found.</td></tr>';
                    return;
                }

                data.forEach(job => {
                    const exceptionShort = job.exception.substring(0, 100) + '...';
                    const row = `
                <tr>
                    <td>${job.id}</td>
                    <td>${job.connection}</td>
                    <td>${job.queue}</td>
                    <td>${new Date(job.failed_at).toLocaleString()}</td>
                    <td title="${job.exception}">${exceptionShort}</td>
                    <td>
                        <button class="btn btn-sm btn-info me-1" onclick="retryJob(${job.id})">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteJob(${job.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                    tbody.innerHTML += row;
                });

                renderPagination(response.data.data);

            } catch (error) {
                console.error('Error fetching jobs:', error);
                toastr.error('Failed to load jobs');
            }
        }

        async function retryJob(id) {
            if (!confirm('Are you sure you want to retry this job?')) return;
            try {
                // Laravel route requires a parameter. We use 0 as placeholder.
                const url = `{{ route('api.jobs.retry', 0) }}`.replace('/0', '/' + id);
                await axios.post(url);
                toastr.success('Job queued for retry');
                fetchJobs();
            } catch (error) {
                console.error('Error retrying job:', error);
                toastr.error('Failed to retry job');
            }
        }

        async function deleteJob(id) {
            if (!confirm('Are you sure you want to delete this job?')) return;
            try {
                const url = `{{ route('api.jobs.destroy', 0) }}`.replace('/0', '/' + id);
                await axios.delete(url);
                toastr.success('Job deleted successfully');
                fetchJobs();
            } catch (error) {
                console.error('Error deleting job:', error);
                toastr.error('Failed to delete job');
            }
        }

        async function retryAllJobs() {
            if (!confirm('Are you sure you want to retry ALL failed jobs?')) return;
            try {
                await axios.post(`{{ route('api.jobs.retry.all') }}`);
                toastr.success('All jobs queued for retry');
                fetchJobs();
            } catch (error) {
                console.error('Error retrying all jobs:', error);
                toastr.error('Failed to retry all jobs');
            }
        }

        function renderPagination(data) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (data.last_page > 1) {
                // Simple Previous/Next for now
                if (data.prev_page_url) {
                    pagination.innerHTML +=
                        `<li class="page-item"><a class="page-link" href="#" onclick="fetchJobs(${data.current_page - 1})">Previous</a></li>`;
                }
                if (data.next_page_url) {
                    pagination.innerHTML +=
                        `<li class="page-item"><a class="page-link" href="#" onclick="fetchJobs(${data.current_page + 1})">Next</a></li>`;
                }
            }
        }
    </script>
@endpush
