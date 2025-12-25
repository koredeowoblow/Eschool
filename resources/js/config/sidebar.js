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
        roles: ['super_admin', 'school_admin', 'teacher']
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
        roles: ['super_admin', 'school_admin', 'teacher']
    },
    {
        type: 'link',
        key: 'assignments',
        label: 'Assignments',
        icon: 'bi bi-journal-text',
        roles: ['super_admin', 'school_admin', 'teacher', 'student']
    },
    {
        type: 'link',
        key: 'attendance',
        label: 'Attendance',
        icon: 'bi bi-calendar-check',
        roles: ['super_admin', 'school_admin', 'teacher', 'student']
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
        roles: ['super_admin', 'school_admin', 'teacher', 'student']
    },
    {
        type: 'header',
        label: 'Finance & More'
    },
    {
        type: 'link',
        key: 'payments',
        label: 'Payments',
        icon: 'bi bi-credit-card',
        roles: ['super_admin', 'school_admin', 'student']
    },
    {
        type: 'link',
        key: 'library',
        label: 'Library',
        icon: 'bi bi-book',
        roles: ['*']
    },
    {
        type: 'link',
        key: 'reports',
        label: 'Reports',
        icon: 'bi bi-graph-up',
        roles: ['super_admin', 'school_admin', 'teacher']
    }
];
