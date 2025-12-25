export default [
    {
        type: 'header',
        label: 'Academic'
    },
    {
        key: 'students',
        label: 'Students',
        icon: 'bi bi-people',
        roles: ['super_admin', 'school_admin', 'teacher']
    },
    {
        key: 'guardians',
        label: 'Guardians',
        icon: 'bi bi-person-heart',
        roles: ['super_admin', 'school_admin', 'teacher']
    },
    {
        key: 'classes',
        label: 'Classes',
        icon: 'bi bi-grid',
        roles: ['super_admin', 'school_admin', 'teacher']
    },
    {
        key: 'lesson-notes',
        label: 'Lesson Notes',
        icon: 'bi bi-journal-bookmark',
        roles: ['super_admin', 'school_admin', 'teacher']
    },
    {
        key: 'assignments',
        label: 'Assignments',
        icon: 'bi bi-journal-text',
        roles: ['super_admin', 'school_admin', 'teacher', 'student']
    },
    {
        key: 'assignmentSubmissions',
        label: 'Submissions',
        icon: 'bi bi-inboxes',
        roles: ['super_admin', 'school_admin', 'teacher', 'student']
    },
    {
        key: 'attendance',
        label: 'Attendance',
        icon: 'bi bi-calendar-check',
        roles: ['super_admin', 'school_admin', 'teacher', 'student']
    },
    {
        key: 'assessments',
        label: 'Assessments',
        icon: 'bi bi-clipboard-check',
        roles: ['super_admin', 'school_admin', 'teacher', 'student']
    },
    {
        key: 'results',
        label: 'Results',
        icon: 'bi bi-trophy',
        roles: ['super_admin', 'school_admin', 'teacher', 'student']
    },
    {
        key: 'invoices',
        label: 'Invoices',
        icon: 'bi bi-receipt',
        roles: ['super_admin', 'school_admin', 'teacher']
    },
    {
        key: 'reports/academic',
        label: 'Academic Reports',
        icon: 'bi bi-file-earmark-bar-graph',
        roles: ['teacher']
    }
];
