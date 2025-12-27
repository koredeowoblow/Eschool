export default [
    {
        type: 'header',
        label: 'Guardian Dashboard'
    },
    {
        key: 'guardian-children',
        label: 'My Children',
        icon: 'bi bi-people-fill',
        path: '/dashboard', // Guardians often stay on dashboard to see children
        roles: ['guardian']
    },
    {
        key: 'academic-overview',
        label: 'Academic Overview',
        icon: 'bi bi-mortarboard',
        children: [
            {
                key: 'guardian-results',
                label: 'Student Results',
                icon: 'bi bi-award',
                path: '/guardian/results',
                roles: ['guardian']
            },
            {
                key: 'guardian-attendance',
                label: 'Student Attendance',
                icon: 'bi bi-calendar-check',
                path: '/guardian/attendance',
                roles: ['guardian']
            }
        ]
    },
    {
        key: 'finance-overview',
        label: 'Financials',
        icon: 'bi bi-cash-stack',
        children: [
            {
                key: 'guardian-fees',
                label: 'Fee Payments',
                icon: 'bi bi-receipt',
                path: '/guardian/fees',
                roles: ['guardian']
            },
            {
                key: 'guardian-invoices',
                label: 'Pending Invoices',
                icon: 'bi bi-file-earmark-text',
                path: '/guardian/invoices',
                roles: ['guardian']
            }
        ]
    }
];
