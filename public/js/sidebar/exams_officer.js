export default [
    {
        type: 'header',
        label: 'Academic Assessment'
    },
    {
        key: 'exam-results',
        label: 'Results Management',
        icon: 'bi bi-award',
        children: [
            {
                key: 'results-review',
                label: 'Review Results',
                icon: 'bi bi-check2-square',
                path: '/results/review',
                roles: ['exams_officer', 'super_admin', 'school_admin']
            },
            {
                key: 'results-approve',
                label: 'Approve Results',
                icon: 'bi bi-patch-check',
                path: '/results/approve',
                roles: ['exams_officer', 'super_admin', 'school_admin']
            },
            {
                key: 'results-history',
                label: 'Academic History',
                icon: 'bi bi-history',
                path: '/results/history',
                roles: ['exams_officer', 'super_admin', 'school_admin']
            }
        ]
    },
    {
        key: 'exam-reports',
        label: 'Academic Reports',
        icon: 'bi bi-file-earmark-bar-graph',
        path: '/reports/academic',
        roles: ['exams_officer', 'super_admin', 'school_admin']
    }
];
