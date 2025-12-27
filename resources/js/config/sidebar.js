export const sidebarConfig = [
    {
        type: 'header',
        label: 'Main'
    },
    {
        type: 'link',
        key: 'dashboard',
        label: 'Dashboard',
        icon: 'bi bi-speedometer2',
        roles: ['*']
    },
    {
        type: 'header',
        label: 'Academic'
    },
    {
        type: 'link',
        key: 'students',
        label: 'Students',
        icon: 'bi bi-people',
        roles: ['super_admin', 'School Admin', 'Teacher']
    },
    {
        type: 'link',
        key: 'teachers',
        label: 'Teachers',
        icon: 'bi bi-person-badge',
        roles: ['super_admin', 'School Admin']
    },
    {
        type: 'link',
        key: 'classes',
        label: 'Classes',
        icon: 'bi bi-grid',
        roles: ['super_admin', 'School Admin', 'Teacher']
    },
    {
        type: 'link',
        key: 'assignments',
        label: 'Assignments',
        icon: 'bi bi-journal-text',
        roles: ['super_admin', 'School Admin', 'Teacher', 'Student']
    },
    {
        type: 'link',
        key: 'attendance',
        label: 'Attendance',
        icon: 'bi bi-calendar-check',
        roles: ['super_admin', 'School Admin', 'Teacher', 'Student']
    },
    {
        type: 'link',
        key: 'timetables',
        label: 'Timetables',
        icon: 'bi bi-table',
        roles: ['*']
    },
    {
        type: 'link',
        key: 'results',
        label: 'Results',
        icon: 'bi bi-award',
        roles: ['super_admin', 'School Admin', 'Teacher', 'Student']
    },
    {
        type: 'header',
        label: 'Finance'
    },
    {
        type: 'link',
        key: 'finance-overview',
        label: 'Financial Overview',
        icon: 'bi bi-cash-stack',
        path: '/finance',
        permissions: ['finance.view.reports']
    },
    {
        type: 'link',
        key: 'manage-invoices',
        label: 'Invoices',
        icon: 'bi bi-receipt',
        path: '/invoices',
        permissions: ['finance.generate.invoices']
    },
    {
        type: 'header',
        label: 'System'
    },
    {
        type: 'link',
        key: 'audit-logs',
        label: 'Audit Logs',
        icon: 'bi bi-shield-check',
        path: '/audit',
        permissions: ['audit.view.logs']
    },
    {
        type: 'link',
        key: 'my-children',
        label: 'My Children',
        icon: 'bi bi-people-fill',
        path: '/my-children',
        roles: ['Guardian']
    },
    {
        type: 'link',
        key: 'role-management',
        label: 'Role Management',
        icon: 'bi bi-person-badge',
        path: '/roles',
        roles: ['School Admin', 'super_admin']
    }
];
