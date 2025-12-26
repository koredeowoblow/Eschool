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
        roles: ['student']
    },
    {
        type: 'link',
        key: 'assignmentSubmissions',
        label: 'Submissions',
        icon: 'bi bi-inboxes',
        roles: ['student']
    },
    {
        type: 'link',
        key: 'attendance',
        label: 'Attendance',
        icon: 'bi bi-calendar-check',
        roles: ['student']
    },
    {
        type: 'link',
        key: 'results',
        label: 'Results',
        icon: 'bi bi-award',
        roles: ['student']
    },
    {
        type: 'link',
        key: 'reports/academic',
        label: 'Academic Reports',
        icon: 'bi bi-file-earmark-bar-graph',
        roles: ['student']
    },
    {
        type: 'header',
        label: 'Finance'
    },
    {
        type: 'link',
        key: 'my-fees',
        label: 'My Fees',
        icon: 'bi bi-receipt',
        roles: ['student']
    },
    {
        type: 'link',
        key: 'payments',
        label: 'Payment History',
        icon: 'bi bi-credit-card',
        roles: ['student']
    }
];
