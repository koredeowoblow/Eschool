export default [
    {
        type: 'header',
        label: 'Academic (Student)'
    },
    {
        type: 'link',
        key: 'assignments',
        label: 'Assignments',
        icon: 'bi bi-journal-text',
        roles: ['Student']
    },
    {
        type: 'link',
        key: 'assignmentSubmissions',
        label: 'Submissions',
        icon: 'bi bi-inboxes',
        roles: ['Student']
    },
    {
        type: 'link',
        key: 'attendance',
        label: 'Attendance',
        icon: 'bi bi-calendar-check',
        roles: ['Student']
    },
    {
        type: 'link',
        key: 'results',
        label: 'Results',
        icon: 'bi bi-award',
        roles: ['Student']
    },
    {
        type: 'link',
        key: 'reports/academic',
        label: 'Academic Reports',
        icon: 'bi bi-file-earmark-bar-graph',
        roles: ['Student']
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
        roles: ['Student']
    }
];
