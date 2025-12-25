export default [
    {
        type: 'header',
        label: 'Academic (Teacher)'
    },
    {
        type: 'link',
        key: 'students',
        label: 'Students',
        icon: 'bi bi-people',
        roles: ['teacher']
    },
    {
        type: 'link',
        key: 'classes',
        label: 'Classes',
        icon: 'bi bi-grid',
        roles: ['teacher']
    },
    {
        type: 'link',
        key: 'assignments',
        label: 'Assignments',
        icon: 'bi bi-journal-text',
        roles: ['teacher']
    },
    {
        type: 'link',
        key: 'attendance',
        label: 'Attendance',
        icon: 'bi bi-calendar-check',
        roles: ['teacher']
    },
    {
        type: 'link',
        key: 'reports',
        label: 'Reports',
        icon: 'bi bi-graph-up',
        roles: ['teacher']
    }
];
