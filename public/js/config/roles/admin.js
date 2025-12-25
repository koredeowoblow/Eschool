export default [
    {
        type: 'header',
        label: 'Academic (Admin)'
    },
    {
        type: 'link',
        key: 'students',
        label: 'Students',
        icon: 'bi bi-people',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'teachers',
        label: 'Teachers',
        icon: 'bi bi-person-badge',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'classes',
        label: 'Classes',
        icon: 'bi bi-grid',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'header',
        label: 'Finance'
    },
    {
        type: 'link',
        key: 'payments',
        label: 'Payments',
        icon: 'bi bi-credit-card',
        roles: ['super_admin', 'school_admin']
    }
];
