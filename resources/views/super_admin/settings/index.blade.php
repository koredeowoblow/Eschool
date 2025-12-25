@extends('layouts.app')

@section('title', 'System Settings')
@section('header_title', 'Configuration')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card-premium p-4">
                <h5 class="mb-4">General Settings</h5>
                <form id="settings-form" onsubmit="saveSettings(event)">
                    <div class="mb-3">
                        <label class="form-label">Platform Name</label>
                        <input type="text" id="site_name" class="form-control" value="{{ config('app.name') }}">
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode">
                        <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="allow_registration" checked>
                        <label class="form-check-label" for="allow_registration">Allow New School Registrations</label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary-premium">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function saveSettings(e) {
            e.preventDefault();

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
            submitBtn.disabled = true;

            const data = {
                site_name: document.getElementById('site_name').value,
                maintenance_mode: document.getElementById('maintenance_mode').checked,
                allow_registration: document.getElementById('allow_registration').checked
            };

            try {
                const res = await axios.post('/api/v1/settings', data);

                if (res.data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: 'System settings updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Settings save error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to update settings'
                });
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        // Load initial settings
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await axios.get('/api/v1/settings');
                if (res.data.status === 'success') {
                    const settings = res.data.data;
                    document.getElementById('site_name').value = settings.site_name || '';
                    document.getElementById('maintenance_mode').checked = settings.maintenance_mode || false;
                    document.getElementById('allow_registration').checked = settings.allow_registration ||
                    false;
                }
            } catch (error) {
                console.error('Failed to load settings:', error);
            }
        });
    </script>
@endsection
