<script>
    try {
        window.AppConfig = {
            appName: "{{ config('app.name', 'eSchool') }}",
            active_session: @json(auth()->user()?->school?->activeSession ?? null),
            user: {
                id: "{{ auth()->id() }}",
                roles: @json(auth()->user()?->getRoleNames() ?? []),
                permissions: @json(auth()->user()?->getAllPermissions()->pluck('name') ?? [])
            }
        };
    } catch (e) {
        console.error('CRITICAL: AppConfig failed to parse', e);
    }
</script>
