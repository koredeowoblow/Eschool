export default [
    {
        type: 'header',
        label: 'Finance Management'
    },
    {
        key: 'finance-fees',
        label: 'Fees Management',
        icon: 'bi bi-list-check',
        children: [
            {
                key: 'fees',
                label: 'Fees List',
                icon: 'bi bi-card-checklist',
                path: '/fees',
                roles: ['finance_officer', 'super_admin', 'school_admin']
            },
            {
                key: 'fees-assign',
                label: 'Assign Fees',
                icon: 'bi bi-person-plus',
                path: '/fees/assign',
                roles: ['finance_officer', 'super_admin', 'school_admin']
            },
            {
                key: 'fee-types',
                label: 'Fee Categories',
                icon: 'bi bi-tags',
                path: '/fee-types',
                roles: ['finance_officer', 'super_admin', 'school_admin']
            }
        ]
    },
    {
        key: 'finance-billing',
        label: 'Invoices & Payments',
        icon: 'bi bi-cash-stack',
        children: [
            {
                key: 'invoices',
                label: 'Invoices',
                icon: 'bi bi-receipt',
                path: '/invoices',
                roles: ['finance_officer', 'super_admin', 'school_admin']
            },
            {
                key: 'payments',
                label: 'Record Payment',
                icon: 'bi bi-credit-card',
                path: '/payments',
                roles: ['finance_officer', 'super_admin', 'school_admin']
            },
            {
                key: 'payment-history',
                label: 'Payment History',
                icon: 'bi bi-clock-history',
                path: '/fees/payments',
                roles: ['finance_officer', 'super_admin', 'school_admin']
            }
        ]
    },
    {
        key: 'finance-reports',
        label: 'Financial Reports',
        icon: 'bi bi-graph-up-arrow',
        path: '/reports/finance',
        roles: ['finance_officer', 'super_admin', 'school_admin']
    }
];
