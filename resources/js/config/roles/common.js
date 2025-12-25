export default [
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
        type: 'link',
        key: 'chats',
        label: 'Chats',
        icon: 'bi bi-chat-dots',
        roles: ['*']
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
        key: 'library',
        label: 'Library',
        icon: 'bi bi-book',
        roles: ['*']
    }
];
