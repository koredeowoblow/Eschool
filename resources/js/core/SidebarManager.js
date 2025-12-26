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
            // console.log('Sidebar: Loaded from cache');
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
                // console.log('Sidebar: Update detected, re-rendering');
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

            const roles = (this.config.user?.roles || []).map(r => String(r).toLowerCase());
            const isSuperAdmin = roles.includes('super_admin');

            if (isSuperAdmin) {
                const superAdmin = await import('../sidebar/super_admin.js');
                items.push(...superAdmin.default);
            }

            if (roles.includes('school_admin') || isSuperAdmin) {
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

            // Filter items based on permissions
            items = this.filterByPermission(items, roles, isSuperAdmin);

        } catch (e) {
            console.error('Sidebar: Error building items', e);
        }
        return items;
    }

    filterByPermission(items, userRoles, isSuperAdmin) {
        if (isSuperAdmin) return items;

        const userPermissions = this.config.user?.permissions || [];

        return items.filter(item => {
            // Check Roles first (if defined)
            if (item.roles && item.roles.length > 0) {
                // Skip role check if it's '*', otherwise check match
                if (!item.roles.includes('*')) {
                    const hasRole = item.roles.some(r => userRoles.includes(r.toLowerCase()));
                    if (!hasRole) return false;
                }
            }

            // Check Permissions
            if (item.permissions && item.permissions.length > 0) {
                const hasPermission = item.permissions.some(p => userPermissions.includes(p));
                if (!hasPermission) return false;
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

        const brand = document.createElement('div');
        brand.className = 'sidebar-brand p-4 border-bottom';
        const brandIcon = document.createElement('div');
        brandIcon.className = 'sidebar-skeleton-icon';
        const brandName = document.createElement('div');
        brandName.className = 'sidebar-skeleton-text';
        brand.append(brandIcon, brandName);
        this.root.appendChild(brand);

        const nav = document.createElement('nav');
        nav.className = 'sidebar-nav p-2';

        for (let i = 0; i < 8; i++) {
            const item = document.createElement('div');
            item.className = 'sidebar-skeleton';
            const icon = document.createElement('div');
            icon.className = 'sidebar-skeleton-icon';
            const text = document.createElement('div');
            text.className = 'sidebar-skeleton-text';
            if (i % 3 === 0) text.style.width = '60%';
            item.append(icon, text);
            nav.appendChild(item);
        }
        this.root.appendChild(nav);
    }

    render() {
        this.root.replaceChildren();

        const brand = document.createElement('div');
        brand.className = 'sidebar-brand p-4 border-bottom';
        const brandLink = document.createElement('a');
        brandLink.href = '/dashboard';
        brandLink.className = 'd-flex align-items-center gap-2 text-decoration-none';

        const brandIcon = document.createElement('i');
        brandIcon.className = 'bi bi-mortarboard-fill text-primary fs-3';

        const brandName = document.createElement('span');
        brandName.className = 'fs-4 fw-bold text-gradient';
        brandName.textContent = this.config.appName || 'eSchool';

        brandLink.append(brandIcon, brandName);
        brand.appendChild(brandLink);
        this.root.appendChild(brand);

        const nav = document.createElement('nav');
        nav.className = 'sidebar-nav';

        this.items.forEach(item => {
            if (item.type === 'header') {
                const header = document.createElement('div');
                header.className = 'sidebar-header text-muted fw-bold small px-3 mt-3 mb-2 text-uppercase';
                header.textContent = item.label;
                nav.appendChild(header);
                return;
            }

            if (item.children && item.children.length > 0) {
                const wrapper = document.createElement('div');
                wrapper.className = 'sidebar-item-group mb-1';

                const parent = document.createElement('a');
                parent.href = '#';
                parent.className = 'sidebar-link d-flex align-items-center justify-content-between';
                parent.dataset.action = 'toggle-submenu';

                const leftPart = document.createElement('div');
                leftPart.className = 'd-flex align-items-center gap-2';
                const parentIcon = document.createElement('i');
                parentIcon.className = item.icon || 'bi bi-circle';
                const parentLabel = document.createElement('span');
                parentLabel.textContent = item.label;
                leftPart.append(parentIcon, parentLabel);

                const chevron = document.createElement('i');
                chevron.className = 'bi bi-chevron-down small transition-transform-gpu';

                parent.append(leftPart, chevron);

                const submenu = document.createElement('div');
                submenu.className = 'collapse sidebar-submenu ps-3';

                item.children.forEach(child => {
                    submenu.appendChild(this.createLink(child));
                });

                if (submenu.querySelector('.active')) {
                    submenu.classList.add('show');
                    parent.classList.add('expanded');
                    const chevron = parent.querySelector('.bi-chevron-down');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }

                wrapper.appendChild(parent);
                wrapper.appendChild(submenu);
                nav.appendChild(wrapper);
            } else {
                nav.appendChild(this.createLink(item));
            }
        });

        // Logout Link
        const logout = document.createElement('a');
        logout.href = '#';
        logout.className = 'sidebar-link text-danger mt-auto mb-3';
        logout.dataset.action = 'logout';

        const logoutIcon = document.createElement('i');
        logoutIcon.className = 'bi bi-box-arrow-left';
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

        this.applyActiveState();
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
                // Check if already animating to prevent jank
                if (submenu.classList.contains('collapsing')) return;

                const bsCollapse = bootstrap.Collapse.getInstance(submenu) || new bootstrap.Collapse(submenu);
                bsCollapse.toggle();

                // Track state for chevron/link
                const isExpanding = !submenu.classList.contains('show');
                trigger.classList.toggle('expanded', isExpanding);
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
        const activeLinks = this.root.querySelectorAll('.sidebar-link.active');
        activeLinks.forEach(link => {
            const group = link.closest('.sidebar-item-group');
            if (group) {
                const collapse = group.querySelector('.collapse');
                const trigger = group.querySelector('[data-bs-toggle="collapse"]');
                if (collapse && !collapse.classList.contains('show')) {
                    // Use Bootstrap's own collapse for smooth entry
                    const bsCollapse = bootstrap.Collapse.getInstance(collapse) || new bootstrap.Collapse(collapse, { toggle: false });
                    bsCollapse.show();

                    if (trigger) {
                        trigger.classList.add('expanded');
                        const chevron = trigger.querySelector('.bi-chevron-down');
                        if (chevron) chevron.style.transform = 'rotate(180deg)';
                    }
                }
            }
        });
    }

    createLink(item) {
        const a = document.createElement('a');

        if (item.action === 'logout') {
            a.href = '#';
            a.dataset.action = 'logout';
        } else {
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
        }

        a.className = 'sidebar-link';
        const i = document.createElement('i');
        i.className = item.icon || 'bi bi-circle';
        const span = document.createElement('span');
        span.textContent = item.label;
        a.append(i, span);

        try {
            const currentPath = window.location.pathname;
            const targetPath = new URL(a.href, window.location.href).pathname;
            if (currentPath === targetPath || (currentPath === '/' && targetPath === '/dashboard')) {
                a.classList.add('active');
            }
        } catch (e) { }

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
