<script>
    try {
        window.AppConfig = {
            appName: "{{ config('app.name', 'eSchool') }}",
            active_session: @json(auth()->user()?->school?->activeSession ?? null)
        };
    } catch (e) {
        console.error('CRITICAL: AppConfig failed to parse', e);
    }
</script>
