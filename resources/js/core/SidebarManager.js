// No static import for sidebarConfig
// Dynamic imports will be used

export class SidebarManager {
    constructor() {
        this.root = document.getElementById('sidebar-root');
        this.config = window.AppConfig;
        this.menuItems = [];
    }

    async init() {
        if (!this.root || !this.config) return;

        // Load common config
        try {
            const commonModule = await import('../config/roles/common.js');
            this.menuItems = [...commonModule.default];
        } catch (e) {
            console.error('Failed to load common sidebar', e);
        }

        // Load role specific configs
        // Normalize roles to avoid case sensitivity issues
        const rawRoles = this.config.user.roles || [];
        this.userRoles = rawRoles.map(r => String(r).toLowerCase());

        console.log('SidebarManager: User Roles detected:', this.userRoles);

        const roles = this.userRoles;

        // Map roles to config files (simplified mapping)
        if (roles.includes('super_admin') || roles.includes('school_admin')) {
            try {
                const module = await import('../config/roles/admin.js');
                this.menuItems = [...this.menuItems, ...module.default];
            } catch (e) {
                console.error('Failed to load admin sidebar', e);
            }
        }

        if (roles.includes('teacher')) {
            try {
                const module = await import('../config/roles/teacher.js');
                this.menuItems = [...this.menuItems, ...module.default];
            } catch (e) {
                console.error('Failed to load teacher sidebar', e);
            }
        }

        if (roles.includes('student')) {
            console.log('SidebarManager: Attempting to load student sidebar...');
            try {
                const module = await import('../config/roles/student.js');
                console.log('SidebarManager: Student module loaded', module.default);
                this.menuItems = [...this.menuItems, ...module.default];
            } catch (e) {
                console.error('Failed to load student sidebar', e);
            }
        } else {
            console.log('SidebarManager: Role "student" not found in user roles:', roles);
        }

        console.log('SidebarManager: Final menu items:', this.menuItems);
        this.render();
        this.attachEvents();
    }

    render() {
        // Wrapper
        const sidebarHtml = document.createElement('div');
        sidebarHtml.className = 'd-flex flex-column h-100';

        // Brand
        const brandHtml = `
            <div class="p-4 border-bottom">
                <a href="${this.config.routes.dashboard}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <i class="bi bi-mortarboard-fill text-primary fs-3"></i>
                    <span class="fs-4 fw-bold text-gradient">eSchool</span>
                </a>
            </div>
        `;
        sidebarHtml.innerHTML = brandHtml;

        // Nav
        const nav = document.createElement('nav');
        nav.className = 'p-3 flex-grow-1 overflow-auto';

        this.menuItems.forEach(item => {
            if (item.type === 'header') {
                const header = document.createElement('small');
                header.className = 'text-uppercase text-muted fw-bold px-3 mt-4 mb-2 d-block';
                header.style.fontSize = '0.7rem';
                header.textContent = item.label;
                if (item.label === 'Main') header.classList.remove('mt-4');
                nav.appendChild(header);
            } else if (item.type === 'link') {
                const hasPermission = item.roles.includes('*') || this.hasRole(item.roles);
                console.log(`SidebarManager: Rendering "${item.label}"? ${hasPermission} (Roles: ${item.roles.join(',')})`);

                if (hasPermission) {
                    const link = document.createElement('a');
                    link.href = this.config.routes[item.key] || '#';
                    link.className = 'nav-link-premium';

                    if (link.href !== '#' && window.location.href.includes(link.href)) {
                        link.classList.add('active');
                    }

                    link.innerHTML = `<i class="${item.icon}"></i> ${item.label}`;
                    nav.appendChild(link);
                }
            }
        });

        // Logout
        const logoutDiv = document.createElement('div');
        logoutDiv.className = 'mt-5 border-top pt-3';
        logoutDiv.innerHTML = `
            <a href="#" id="logoutBtnJS" class="nav-link-premium text-danger">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        `;
        nav.appendChild(logoutDiv);

        sidebarHtml.appendChild(nav);
        this.root.appendChild(sidebarHtml);
    }

    hasRole(allowedRoles) {
        if (allowedRoles.includes('*')) return true;
        // Use the normalized userRoles stored in init
        const userRoles = this.userRoles || [];
        // Ensure allowedRoles are also lowercased for comparison? 
        // Ideally the config files are already lowercase, but safety first not strictly needed if we trust our config.
        // But comparing lowercase to lowercase is safest.
        return allowedRoles.some(role => userRoles.includes(String(role).toLowerCase()));
    }

    attachEvents() {
        const logoutBtn = document.getElementById('logoutBtnJS');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.logout();
            });
        }
    }

    logout() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.config.routes.logout;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = this.config.csrf;
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }
}
