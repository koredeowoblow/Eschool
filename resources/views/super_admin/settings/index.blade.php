@extends('layouts.app')

@section('title', 'System Settings')
@section('header_title', 'Configuration')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="col-lg-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-4">
                <h5 class=" mb-6 ">General Settings</h5>
                <form id="settings-form" onsubmit="saveSettings(event)">
                    <div class=" mb-6 ">
                        <label class="block text-sm font-medium text-gray-700  mb-6 ">Platform Name</label>
                        <input type="text" id="site_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="{{ config('app.name') }}">
                    </div>

                    <div class=" mb-6  form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode">
                        <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                    </div>

                    <div class=" mb-6  form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="allow_registration" checked>
                        <label class="form-check-label" for="allow_registration">Allow New School Registrations</label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Changes</button>
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
