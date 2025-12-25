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
        key: 'guardians',
        label: 'Guardians',
        icon: 'bi bi-people-fill',
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
        type: 'link',
        key: 'reports/academic',
        label: 'Academic Reports',
        icon: 'bi bi-file-earmark-bar-graph',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'assignments',
        label: 'Assignments',
        icon: 'bi bi-journal-text',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'lessonNotes',
        label: 'Lesson Notes',
        icon: 'bi bi-journal-bookmark',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'assignmentSubmissions',
        label: 'Submissions',
        icon: 'bi bi-inboxes',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'attendance',
        label: 'Attendance',
        icon: 'bi bi-calendar-check',
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
    },
    {
        type: 'link',
        key: 'reports',
        label: 'Reports',
        icon: 'bi bi-graph-up',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'assessments',
        label: 'Assessments',
        icon: 'bi bi-clipboard-check',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'results',
        label: 'Results',
        icon: 'bi bi-award',
        roles: ['super_admin', 'school_admin']
    },
    {
        type: 'link',
        key: 'settings',
        label: 'Settings',
        icon: 'bi bi-gear',
        roles: ['super_admin', 'school_admin']
    }
];
