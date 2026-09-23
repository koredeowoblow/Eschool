export class SidebarManager {
    constructor() {
        this.root = document.getElementById('sidebar-root');
        this.config = window.AppConfig || {};
        this.items = [];
    }

    async init() {
        if (!this.root) return;

        // Try to fetch user context first if not provided in config
        if (!this.config.user) {
            try {
                const res = await axios.get('/api/v1/user');
                this.config.user = res.data?.data || res.data;
            } catch (e) {
                console.warn('Sidebar: Failed to fetch user context', e);
            }
        }

        // 1. Try to load from cache first for immediate rendering
        const cached = this.loadFromCache();
        if (cached) {
            this.items = cached;
            this.render();
        } else {
            // Show skeleton loading state
            this.showSkeleton();
        }

        // 2. Build fresh items
        try {
            const freshItems = await this.buildItems();

            // 3. Compare and update if different
            if (JSON.stringify(freshItems) !== JSON.stringify(this.items)) {
                this.items = freshItems;
                this.render();
                this.saveToCache(freshItems);
            }
        } catch (e) {
            console.error('Sidebar: Build failed', e);
        }
    }

    async buildItems() {
        let items = [];
        try {
            const common = await import('../sidebar/common.js');
            items = [...common.default];

            const roles = (this.config.user?.roles || []).map(r =>
                String(r).toLowerCase().replace(/\s+/g, '_')
            );
            const isSuperAdmin = roles.includes('super_admin');

            if (isSuperAdmin) {
                const superAdmin = await import('../sidebar/super_admin.js');
                items.push(...superAdmin.default);
            }

            if (roles.includes('School Admin') || isSuperAdmin) {
                const admin = await import('../sidebar/admin.js');
                items.push(...admin.default);
            }

            if (roles.includes('teacher')) {
                const teacher = await import('../sidebar/teacher.js');
                items.push(...teacher.default);
            }

            if (roles.includes('student')) {
                const student = await import('../sidebar/student.js');
                items.push(...student.default);
            }

            if (roles.includes('guardian')) {
                const guardian = await import('../sidebar/guardian.js');
                items.push(...guardian.default);
            }

            if (roles.includes('finance_officer')) {
                const finance = await import('../sidebar/finance_officer.js');
                items.push(...finance.default);
            }

            if (roles.includes('exams_officer')) {
                const exams = await import('../sidebar/exams_officer.js');
                items.push(...exams.default);
            }

            // Filter items based on permissions
            items = this.filterByPermission(items, roles, isSuperAdmin);

        } catch (e) {
            console.error('Sidebar: Error building items', e);
        }
        return items;
    }

    filterByPermission(items, userRoles, isSuperAdmin) {
        if (isSuperAdmin) return items;

        return items.filter(item => {
            if (item.roles && item.roles.length > 0) {
                const hasRole = item.roles.some(r =>
                    userRoles.includes(String(r).toLowerCase().replace(/\s+/g, '_'))
                );
                if (!hasRole) return false;
            }

            if (item.children && item.children.length > 0) {
                item.children = this.filterByPermission(item.children, userRoles, isSuperAdmin);
                return item.children.length > 0;
            }

            return true;
        });
    }

    getCacheKey() {
        const userId = this.config.user?.id || 'guest';
        return `sidebar_v2_${userId}`;
    }

    loadFromCache() {
        try {
            const json = localStorage.getItem(this.getCacheKey());
            return json ? JSON.parse(json) : null;
        } catch (e) {
            return null;
        }
    }

    saveToCache(items) {
        try {
            localStorage.setItem(this.getCacheKey(), JSON.stringify(items));
        } catch (e) { }
    }

    showSkeleton() {
        this.root.replaceChildren();

        const nav = document.createElement('nav');
        nav.className = 'p-4 space-y-4';

        for (let i = 0; i < 6; i++) {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-3 animate-pulse';
            
            const icon = document.createElement('div');
            icon.className = 'w-5 h-5 bg-slate-200 rounded-md';
            
            const text = document.createElement('div');
            text.className = 'h-4 bg-slate-200 rounded-md flex-1';
            if (i % 3 === 0) text.style.width = '60%';
            
            item.append(icon, text);
            nav.appendChild(item);
        }
        this.root.appendChild(nav);
    }

    render() {
        this.root.replaceChildren();

        const nav = document.createElement('nav');
        nav.className = 'px-4 py-6 flex flex-col gap-1';

        this.items.forEach(item => {
            if (item.type === 'header') {
                const header = document.createElement('div');
                header.className = 'px-3 mt-6 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider';
                header.textContent = item.label;
                nav.appendChild(header);
                return;
            }

            if (item.children && item.children.length > 0) {
                const wrapper = document.createElement('div');
                wrapper.className = 'sidebar-item-group flex flex-col gap-1 mb-1';

                const parent = document.createElement('a');
                parent.href = '#';
                parent.className = 'flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-600 font-medium hover:bg-slate-100 hover:text-blue-600 transition-colors w-full cursor-pointer';
                parent.dataset.action = 'toggle-submenu';

                const leftPart = document.createElement('div');
                leftPart.className = 'flex items-center gap-3';
                const parentIcon = document.createElement('i');
                parentIcon.className = (item.icon || 'bi bi-circle') + ' text-lg';
                const parentLabel = document.createElement('span');
                parentLabel.textContent = item.label;
                leftPart.append(parentIcon, parentLabel);

                const chevron = document.createElement('i');
                chevron.className = 'bi bi-chevron-down text-sm transition-transform duration-300';

                parent.append(leftPart, chevron);

                const submenu = document.createElement('div');
                submenu.className = 'sidebar-submenu pl-9 pr-2 flex-col gap-1 hidden';

                item.children.forEach(child => {
                    submenu.appendChild(this.createLink(child, true));
                });

                wrapper.appendChild(parent);
                wrapper.appendChild(submenu);
                nav.appendChild(wrapper);
            } else {
                nav.appendChild(this.createLink(item, false));
            }
        });

        // Logout Link
        const logout = document.createElement('a');
        logout.href = '#';
        logout.className = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 font-medium hover:bg-red-50 transition-colors mt-8';
        logout.dataset.action = 'logout';

        const logoutIcon = document.createElement('i');
        logoutIcon.className = 'bi bi-box-arrow-left text-lg';
        const logoutLabel = document.createElement('span');
        logoutLabel.textContent = 'Logout';

        logout.append(logoutIcon, logoutLabel);
        nav.appendChild(logout);

        this.root.appendChild(nav);

        // Use single delegated listener on the root if not already attached
        if (!this.delegatedListenerAttached) {
            this.root.addEventListener('click', (e) => this.handleDelegatedClick(e));
            this.delegatedListenerAttached = true;
        }

        // Wait a tick for DOM to render then apply active state
        setTimeout(() => this.applyActiveState(), 50);
    }

    handleDelegatedClick(e) {
        const trigger = e.target.closest('[data-action]');
        if (!trigger) return;

        const action = trigger.dataset.action;

        if (action === 'toggle-submenu') {
            e.preventDefault();
            const group = trigger.closest('.sidebar-item-group');
            const submenu = group?.querySelector('.sidebar-submenu');
            if (submenu) {
                const isExpanding = submenu.classList.contains('hidden');
                
                if (isExpanding) {
                    submenu.classList.remove('hidden');
                    submenu.classList.add('flex');
                    trigger.classList.add('bg-slate-50', 'text-blue-600');
                    trigger.classList.remove('text-slate-600');
                } else {
                    submenu.classList.add('hidden');
                    submenu.classList.remove('flex');
                    trigger.classList.remove('bg-slate-50', 'text-blue-600');
                    trigger.classList.add('text-slate-600');
                }

                const chevron = trigger.querySelector('.bi-chevron-down');
                if (chevron) {
                    chevron.style.transform = isExpanding ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            }
        } else if (action === 'logout') {
            e.preventDefault();
            this.logout();
        }
    }

    applyActiveState() {
        const activeLinks = this.root.querySelectorAll('.sidebar-link-active');
        activeLinks.forEach(link => {
            const group = link.closest('.sidebar-item-group');
            if (group) {
                const submenu = group.querySelector('.sidebar-submenu');
                const trigger = group.querySelector('[data-action="toggle-submenu"]');
                if (submenu && trigger) {
                    submenu.classList.remove('hidden');
                    submenu.classList.add('flex');
                    trigger.classList.add('bg-slate-50', 'text-blue-600');
                    trigger.classList.remove('text-slate-600');
                    const chevron = trigger.querySelector('.bi-chevron-down');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }
            }
        });
    }

    createLink(item, isChild) {
        const a = document.createElement('a');

        let href = '#';
        if (item.path) {
            href = item.path;
        } else if (item.key) {
            const slug = item.key
                .replace(/[A-Z]/g, m => '-' + m.toLowerCase())
                .replace(/_/g, '-');
            href = `/${slug}`;
        }
        a.href = href;
        
        const baseClasses = isChild 
            ? 'flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors text-sm'
            : 'flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors mb-1';

        const i = document.createElement('i');
        i.className = (item.icon || 'bi bi-circle') + (isChild ? ' text-sm' : ' text-lg');
        const span = document.createElement('span');
        span.textContent = item.label;
        a.append(i, span);

        let isActive = false;
        try {
            const currentPath = window.location.pathname;
            const targetPath = new URL(a.href, window.location.href).pathname;
            if (currentPath === targetPath || (currentPath === '/' && targetPath === '/dashboard') || (currentPath.startsWith(targetPath) && targetPath !== '/')) {
                isActive = true;
            }
        } catch (e) { }

        if (isActive) {
            a.className = baseClasses + (isChild ? ' text-blue-600 font-bold bg-blue-50/50 sidebar-link-active' : ' bg-blue-50 text-blue-600 font-bold sidebar-link-active');
        } else {
            a.className = baseClasses + ' text-slate-600 hover:bg-slate-100 hover:text-blue-600';
        }

        return a;
    }

    logout() {
        try {
            localStorage.removeItem(this.getCacheKey());
        } catch (e) { }

        if (window.App && typeof window.App.logout === 'function') {
            window.App.logout();
        } else {
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = '/';
        }
    }
}
