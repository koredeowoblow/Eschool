export default [
    {
        type: 'header',
        label: 'Platform Administration'
    },
    {
        key: 'schools',
        label: 'Schools',
        icon: 'bi bi-building',
        path: '/super-admin/schools'
    },
    {
        key: 'users',
        label: 'Global Users',
        icon: 'bi bi-people-fill',
        path: '/super-admin/users'
    },
    {
        key: 'plans',
        label: 'Membership Plans',
        icon: 'bi bi-card-list',
        path: '/super-admin/plans'
    },
    {
        key: 'payments',
        label: 'Platform Payments',
        icon: 'bi bi-currency-dollar',
        path: '/super-admin/payments'
    },
    {
        key: 'settings',
        label: 'System Settings',
        icon: 'bi bi-gear-fill',
        path: '/super-admin/settings'
    },
    {
        type: 'header',
        label: 'System Monitoring'
    },
    {
        key: 'audit-logs',
        label: 'Audit Logs',
        icon: 'bi bi-shield-check',
        path: '/audit'
    },
    {
        key: 'role-management',
        label: 'Role Management',
        icon: 'bi bi-person-badge',
        path: '/roles'
    }
];
