export default [
    {
        key: 'academic',
        label: 'Academic',
        icon: 'bi bi-mortarboard',
        children: [
            {
                key: 'sessions',
                label: 'Sessions',
                icon: 'bi bi-calendar-range',
                roles: ['super_admin', 'school_admin', 'teacher']
            },
            {
                key: 'terms',
                label: 'Terms',
                icon: 'bi bi-calendar3',
                roles: ['super_admin', 'school_admin', 'teacher']
            },

            {
                key: 'sections',
                label: 'Sections',
                icon: 'bi bi-collection',
                roles: ['super_admin', 'school_admin', 'teacher']
            }, {
                key: 'classes',
                label: 'Classes',
                icon: 'bi bi-grid',
                roles: ['super_admin', 'school_admin', 'teacher']
            },
            {
                key: 'subjects',
                label: 'Subjects',
                icon: 'bi bi-book-half',
                roles: ['super_admin', 'school_admin', 'teacher']
            },
            {
                key: 'subject-assignments',
                label: 'Assign Subjects',
                icon: 'bi bi-link-45deg',
                roles: ['super_admin', 'school_admin']
            }
        ]
    },
    {
        key: 'teachers',
        label: 'Teachers',
        icon: 'bi bi-person-badge',
        roles: ['super_admin', 'school_admin']
    },
    {
        key: 'students',
        label: 'Students',
        icon: 'bi bi-people',
        children: [
            {
                key: 'students',
                label: 'Students',
                icon: 'bi bi-person',
                roles: ['super_admin', 'school_admin', 'teacher']
            },
            {
                key: 'guardians',
                label: 'Guardians',
                icon: 'bi bi-person-heart',
                roles: ['super_admin', 'school_admin', 'teacher']
            },
            {
                key: 'promotions',
                label: 'Student Promotions',
                icon: 'bi bi-arrow-up-circle',
                roles: ['super_admin', 'school_admin', 'teacher']
            }
        ]
    },

    {
        key: 'assessment',
        label: 'Assessment',
        icon: 'bi bi-journal-check',
        children: [
            {
                key: 'assignments',
                label: 'Assignments',
                icon: 'bi bi-journal-text',
                roles: ['super_admin', 'school_admin', 'teacher', 'student']
            },
            {
                key: 'assignment-submissions',
                label: 'Submissions',
                icon: 'bi bi-file-earmark-text',
                roles: ['super_admin', 'school_admin', 'teacher']
            },
            {
                key: 'assessments',
                label: 'Assessments',
                icon: 'bi bi-pencil-square',
                roles: ['super_admin', 'school_admin', 'teacher', 'student']
            },
            {
                key: 'results',
                label: 'Results',
                icon: 'bi bi-trophy',
                roles: ['super_admin', 'school_admin', 'teacher', 'student']
            },
            {
                key: 'reports/academic',
                label: 'Academic Reports',
                icon: 'bi bi-file-earmark-bar-graph',
                roles: ['super_admin', 'school_admin']
            }
        ]
    },
    {
        key: 'finance',
        label: 'Finance',
        icon: 'bi bi-cash-coin',
        children: [
            {
                key: 'fees',
                label: 'Fees List',
                icon: 'bi bi-list-check',
                roles: ['super_admin', 'school_admin']
            },
            {
                key: 'fees/assign',
                label: 'Assign Fees',
                icon: 'bi bi-person-plus',
                roles: ['super_admin', 'school_admin']
            },
            {
                key: 'fees/payments',
                label: 'Payment History',
                icon: 'bi bi-clock-history',
                roles: ['super_admin', 'school_admin']
            },
            {
                key: 'payments',
                label: 'General Payments',
                icon: 'bi bi-credit-card',
                roles: ['super_admin', 'school_admin', 'student']
            },
            {
                key: 'invoices',
                label: 'Invoices',
                icon: 'bi bi-receipt',
                roles: ['super_admin', 'school_admin', 'student']
            },
            {
                key: 'fee-types',
                label: 'Fee Categories',
                icon: 'bi bi-tag',
                roles: ['super_admin', 'school_admin']
            }
        ]
    },
    {
        key: 'settings',
        label: 'Settings',
        icon: 'bi bi-gear',
        children: [
            {
                key: 'profile',
                label: 'School Profile',
                icon: 'bi bi-building',
                roles: ['super_admin', 'school_admin']
            },

        ]
    }
];
