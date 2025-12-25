import { SidebarManager } from './core/SidebarManager.js';
const App = (() => {

    /* ================= CORE ================= */

    // SETUP AXIOS GLOBALLY IMMEDIATELY
    const setupAxios = () => {
        const csrf = document.querySelector('meta[name="csrf-token"]');
        const token = localStorage.getItem('auth_token');

        if (window.axios) {
            axios.defaults.withCredentials = true;
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            if (csrf) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.content;
            if (token) axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

            // Add global interceptor for 401/419 (Unauthenticated)
            axios.interceptors.response.use(
                response => {
                    // Also check if the body explicitly says Unauthenticated even with 200 status
                    if (response.data && response.data.success === false &&
                        (response.data.message === 'Unauthenticated' || response.data.message === 'Unauthenticated.')) {
                        console.warn('App: Explicit Unauthenticated response. Redirecting.');
                        localStorage.clear();
                        sessionStorage.clear();
                        window.location.replace('/');
                    }
                    return response;
                },
                error => {
                    if (error.response && ([401, 419].includes(error.response.status) ||
                        (error.response.data && error.response.data.message === 'Unauthenticated.'))) {
                        console.warn('Session expired or unauthorized. Clearing storage and redirecting.');
                        localStorage.clear();
                        sessionStorage.clear();
                        window.location.replace('/');
                    }
                    return Promise.reject(error);
                }
            );
        }
    };

    // Run immediately
    setupAxios();

    let userPromise = null;
    let clickHandlerAttached = false;
    const DEBUG = false;

    // --- State Management ---
    const State = {
        user: null,
        currentStudentId: null,
        controllers: {
            table: null
        },
        table: {
            sortField: null,
            sortOrder: 'asc',
            lastUrl: null,
            entityType: null,
            tbodyId: null
        }
    };

    // --- Utilities ---
    const escapeText = (str) => {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    /**
     * Proper Sanitization for legacy rows using template literals.
     * Always escapes unless the value is explicitly marked as TrustedHTML.
     */
    const safeHTML = (strings, ...values) => {
        return strings.reduce((acc, str, i) => {
            let value = (values[i] !== undefined && values[i] !== null) ? values[i] : '';

            // Only allow raw inject if it's explicitly trusted or not a string
            if (typeof value === 'string') {
                value = escapeText(value);
            } else if (value && typeof value === 'object' && value.__isSafe) {
                value = value.html;
            } else if (typeof value !== 'undefined' && value !== null) {
                value = escapeText(String(value));
            }

            return acc + str + value;
        }, '');
    };

    /**
     * Wrapper to explicitly trust a string as HTML (Use sparingly!)
     */
    const trust = (html) => ({ html, __isSafe: true });

    const fetchUser = async (force = false) => {
        if (!force && AppInstance.user) return AppInstance.user;
        if (!force && window.AppConfig?.user) {
            AppInstance.user = window.AppConfig.user;
            return AppInstance.user;
        }
        if (userPromise && !force) return userPromise;

        userPromise = axios.get('/api/v1/user')
            .then(res => {
                const user = res.data?.data || res.data;
                State.user = user;
                if (DEBUG) console.log('App: User context loaded', user);
                if (window.AppConfig) window.AppConfig.user = user;

                // Handle Guardian Student Selection
                if (user.roles?.includes('guardian') && user.guardian?.students?.length > 0) {
                    const savedId = localStorage.getItem('guardian_selected_student_id');
                    const students = user.guardian.students;
                    const exists = students.find(s => String(s.id) === String(savedId));

                    if (!savedId || !exists) {
                        State.currentStudentId = students[0].id;
                        localStorage.setItem('guardian_selected_student_id', students[0].id);
                    } else {
                        State.currentStudentId = savedId;
                    }
                } else if (user.roles?.includes('student')) {
                    State.currentStudentId = user.student?.id;
                }

                return user;
            })
            .catch(err => {
                console.warn('App: User context fetch failed', err);
                return null;
            });

        return userPromise;
    };

    const resetForm = (form) => {
        if (!form) return;
        form.reset();
        clearFormErrors(form);
    };

    const init = async () => {
        // Only attach click handler once to prevent memory leaksfrom duplicate listeners
        if (!clickHandlerAttached) {
            document.addEventListener('click', handleActionClicks);
            document.addEventListener('click', handleSortClicks);
            clickHandlerAttached = true;
        }

        const user = await fetchUser();

        if (DEBUG && user) {
            const roles = (user.roles || []).map(r => String(r).toLowerCase().replace(/\s+/g, '_'));
            console.log('App: Initialized with roles:', roles);
        }

        if (document.getElementById('dashboard-stats-root')) {
            loadDashboard();
        }

        renderStudentSelector();
        enforceSessionLock();
    };

    const renderStudentSelector = () => {
        const user = State.user;
        if (!user || !user.roles?.includes('guardian')) return;

        const students = user.guardian?.students || [];
        if (students.length <= 1) return;

        const container = document.getElementById('guardian-student-selector-container');
        if (!container) return;

        const currentId = AppInstance.currentStudentId;

        let html = `
            <div class="dropdown">
                <button class="btn btn-white border shadow-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-people text-primary"></i>
                    <span>Viewing: <strong>${escapeText(students.find(s => String(s.id) === String(currentId))?.full_name || 'Select Child')}</strong></span>
                </button>
                <ul class="dropdown-menu shadow border-0">
                    <li class="dropdown-header">Switch Child</li>
        `;

        students.forEach(student => {
            html += `
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 ${String(student.id) === String(currentId) ? 'active' : ''}" 
                       href="#" onclick="event.preventDefault(); App.selectStudent(${student.id})">
                        <i class="bi bi-person"></i>
                        ${escapeText(student.full_name)}
                    </a>
                </li>
            `;
        });

        html += `</ul></div>`;
        container.innerHTML = html;
    };

    const selectStudent = (id) => {
        localStorage.setItem('guardian_selected_student_id', id);
        State.currentStudentId = id;
        location.reload();
    };

    /* ================= SESSION LOCKING & UTILS ================= */

    const enforceSessionLock = () => {
        const session = window.AppConfig?.active_session;
        if (!session || (session.status !== 'closed' && session.status !== 'locked')) return;

        // Visual Indicator
        const header = document.querySelector('.admin-header');
        if (header && !document.getElementById('session-lock-banner')) {
            const banner = document.createElement('div');
            banner.id = 'session-lock-banner';
            banner.className = 'bg-danger text-white text-center py-1 small fw-bold fixed-top';
            banner.style.zIndex = '1060';
            banner.textContent = `SESSION LOCKED: ${session.name} is currently closed. Read-only mode active.`;
            document.body.prepend(banner);
            document.body.style.paddingTop = '24px';
        }

        // Disable Action Buttons
        const buttons = document.querySelectorAll('button[data-bs-target*="create"], button[data-bs-target*="edit"], .btn-primary-premium:not([data-bs-dismiss])');
        buttons.forEach(btn => {
            // Exceptions for navigation or benign actions can be added here
            if (btn.innerText.includes('Search') || btn.innerText.includes('Filter')) return;

            btn.disabled = true;
            btn.classList.add('disabled');
            btn.title = 'Action disabled: Session is closed.';
        });

        // specific class for explicit locking
        document.querySelectorAll('.requires-session-lock').forEach(el => {
            el.disabled = true;
            el.classList.add('disabled');
        });

        // DISABLE FORMS IN MODALS (Effective when modal opens)
        document.addEventListener('shown.bs.modal', () => {
            const session = window.AppConfig?.active_session;
            if (!session || (session.status !== 'closed' && session.status !== 'locked')) return;

            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modal => {
                const form = modal.querySelector('form');
                if (form) {
                    const inputs = form.querySelectorAll('input, select, textarea, button[type="submit"]');
                    inputs.forEach(input => {
                        input.disabled = true;
                    });

                    // Optional: Helper text
                    const footer = modal.querySelector('.modal-footer');
                    if (footer && !footer.querySelector('.lock-msg')) {
                        const msg = document.createElement('small');
                        msg.className = 'text-danger fw-bold me-3 lock-msg';
                        msg.textContent = 'Session Locked - Read Only';
                        footer.prepend(msg);
                    }
                }
            });
        });
    };

    /* ================= DASHBOARD ================= */

    const loadGradingOptions = async (selectId, selectedValue = null) => {
        const select = document.getElementById(selectId);
        if (!select) return;

        try {
            const res = await axios.get('/api/v1/settings/grading-scale');
            const options = res.data?.data || [];

            select.replaceChildren();
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = 'Select Grade';
            select.appendChild(defaultOpt);

            options.forEach(opt => {
                const el = document.createElement('option');
                el.value = opt.grade;
                el.textContent = `${opt.grade} (${opt.min}-${opt.max})`;
                if (selectedValue && String(selectedValue) === String(opt.grade)) {
                    el.selected = true;
                }
                select.appendChild(el);
            });
        } catch (err) {
            console.error('Failed to load grading scale', err);
            select.innerHTML = '<option value="">Error loading grades</option>';
        }
    };

    /* ================= DASHBOARD ================= */

    const loadDashboard = async () => {
        const root = document.getElementById('dashboard-stats-root');
        if (!root) return;

        root.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 text-muted italic">Gathering insights...</div>
            </div>
        `;

        try {
            const res = await axios.get('/api/v1/dashboard/stats');
            if (!res?.data?.data) throw new Error();

            root.replaceChildren();

            const data = res.data.data;
            if (data.platform) {
                renderPlatformStats(root, data.platform);
            } else if (data.student) {
                renderStudentStats(root, data.student);
            } else if (data.teacher) {
                renderTeacherStats(root, data.teacher);
            } else if (data.general) {
                renderSchoolStats(root, data);
            }

            if (data.charts) {
                renderCharts(data.charts);
            }
        } catch (err) {
            console.error('Dashboard load error:', err);
            root.textContent = 'Failed to load dashboard data.';
        }
    };

    const renderCharts = (charts) => {
        const root = document.getElementById('dashboard-charts-root');
        if (!root) return;
        root.replaceChildren();

        Object.entries(charts).forEach(([key, data], idx) => {
            const col = document.createElement('div');
            col.className = 'col-md-6';

            const card = document.createElement('div');
            card.className = 'card-premium h-100 p-4';
            card.classList.add('animate-in', `stagger-${(idx % 3) + 1}`);

            const title = document.createElement('h6');
            title.className = 'fw-bold text-dark mb-3 text-uppercase small';
            title.textContent = key.replace(/_/g, ' ');

            const canvasContainer = document.createElement('div');
            canvasContainer.style.position = 'relative';
            canvasContainer.style.height = '300px';
            canvasContainer.style.width = '100%';

            const canvas = document.createElement('canvas');
            canvasContainer.appendChild(canvas);

            card.append(title, canvasContainer);
            col.appendChild(card);
            root.appendChild(col);

            new Chart(canvas, getChartConfig(key, data));
        });
    };

    const getChartConfig = (key, data) => {
        const isDark = false; // Prepare for dark mode later
        const colors = {
            primary: '#4f46e5',
            secondary: '#ec4899',
            tertiary: '#10b981',
            text: '#64748b',
            grid: '#e2e8f0'
        };

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1e293b',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: colors.text } },
                y: { grid: { color: colors.grid, borderDash: [5, 5] }, ticks: { color: colors.text } }
            }
        };

        if (key === 'school_growth' || key === 'class_performance') {
            return {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Count',
                        data: data.data,
                        backgroundColor: colors.primary,
                        borderRadius: 4,
                        maxBarThickness: 40
                    }]
                },
                options: commonOptions
            };
        }

        if (key === 'revenue_trends' || key === 'transaction_flow' || key === 'performance_trend') {
            return {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Value',
                        data: data.data,
                        borderColor: colors.secondary,
                        backgroundColor: (ctx) => {
                            const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(236, 72, 153, 0.2)');
                            gradient.addColorStop(1, 'rgba(236, 72, 153, 0)');
                            return gradient;
                        },
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 6
                    }]
                },
                options: commonOptions
            };
        }

        if (key === 'enrollment_distribution') {
            return {
                type: 'doughnut',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [{
                        data: data.map(d => d.value),
                        backgroundColor: [colors.primary, colors.secondary, colors.tertiary, '#f59e0b', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } }
                    },
                    scales: { x: { display: false }, y: { display: false } }
                }
            };
        }

        return {};
    };

    const renderPlatformStats = (root, stats) => {
        const cards = [
            ['Total Schools', stats.total_schools],
            ['Total Users', stats.total_users],
            ['Total Revenue', formatCurrency(stats.total_revenue)]
        ];

        cards.forEach(([label, value], idx) => {
            const card = createStatCard(label, value);
            card.classList.add('animate-in', `stagger-${(idx % 5) + 1}`);
            root.appendChild(card);
        });
    };

    const renderStudentStats = (root, stats) => {
        const cards = [
            createStatCard('Attendance Rate', `${Math.round(stats.attendance)}%`, 'bi-calendar-check'),
            createStatCard('Active Assignments', stats.assignments, 'bi-journal-text'),
            createStatCard('Average Grade', stats.avg_marks.toFixed(1), 'bi-award')
        ];

        cards.forEach((card, idx) => {
            card.classList.add('animate-in', `stagger-${(idx % 5) + 1}`);
            root.appendChild(card);
        });

        const activityRoot = document.getElementById('dashboard-activity-root');
        if (activityRoot && Array.isArray(stats.upcoming_assignments)) {
            renderActivity(activityRoot, stats.upcoming_assignments);
        }
    };

    const renderTeacherStats = (root, stats) => {
        const cards = [
            createStatCard('Total Classes', stats.classes, 'bi-grid'),
            createStatCard('Total Students', stats.students, 'bi-people'),
            createStatCard('Pending Grading', stats.assignments, 'bi-clipboard-check')
        ];

        cards.forEach((card, idx) => {
            card.classList.add('animate-in', `stagger-${(idx % 5) + 1}`);
            root.appendChild(card);
        });

        const activityRoot = document.getElementById('dashboard-activity-root');
        if (activityRoot && stats.academic?.upcoming_assignments) {
            renderActivity(activityRoot, stats.academic.upcoming_assignments);
        }
    };

    const renderSchoolStats = (root, stats) => {
        // Row 1: Academic Overview
        // Row 3: Financial Health
        const cards = [
            createStatCard('Total Students', stats.general?.students || 0, 'bi-people'),
            createStatCard('Active Teachers', stats.general?.teachers || 0, 'bi-person-badge'),
            createStatCard('Total Classes', stats.general?.classes || 0, 'bi-building'),
            createStatCard('Assignments', stats.general?.assignments || 0, 'bi-journal-text'),
            createStatCard('Paid Invoices', stats.finance?.invoices?.paid || 0, 'bi-check-all'),
            createStatCard('Overdue Invoices', stats.finance?.invoices?.overdue || 0, 'bi-exclamation-triangle-fill'),
            createStatCard('Total Revenue', formatCurrency(stats.finance?.payments?.total_amount || 0), 'bi-cash-stack'),
            createStatCard('Month Revenue', formatCurrency(stats.finance?.payments?.this_month_amount || 0), 'bi-wallet2'),
            createStatCard('Outstanding Balance', formatCurrency(stats.finance?.outstanding_balance || 0), 'bi-piggy-bank')
        ];

        cards.forEach((card, idx) => {
            card.classList.add('animate-in', `stagger-${(idx % 5) + 1}`);
            root.appendChild(card);
        });

        const activityRoot = document.getElementById('dashboard-activity-root');
        if (activityRoot && Array.isArray(stats.academic?.upcoming_assignments)) {
            renderActivity(activityRoot, stats.academic.upcoming_assignments);
        }
    };

    const createStatCard = (label, value, icon = 'bi-graph-up') => {
        const col = document.createElement('div');
        col.className = 'col-md-4';

        const card = document.createElement('div');
        card.className = 'card-premium h-100 d-flex align-items-center gap-3 p-4';

        const iconBox = document.createElement('div');
        iconBox.className = 'avatar-md bg-primary-subtle rounded-circle text-primary d-flex align-items-center justify-content-center shadow-sm';
        iconBox.style.minWidth = '54px';
        iconBox.style.height = '54px';
        iconBox.innerHTML = `<i class="bi ${icon} fs-3"></i>`;

        const content = document.createElement('div');
        content.className = 'flex-fill';

        const p = document.createElement('p');
        p.className = 'text-muted text-uppercase fw-semibold small mb-1';
        p.style.letterSpacing = '0.5px';
        p.textContent = label;

        const h = document.createElement('h2');
        h.className = 'h2 mb-0 fw-bold text-dark';
        h.textContent = value;

        content.append(p, h);
        card.append(iconBox, content);
        col.appendChild(card);

        return col;
    };

    const renderActivity = (root, assignments) => {
        root.replaceChildren();

        const wrapper = document.createElement('div');
        wrapper.className = 'card-premium p-3';

        const title = document.createElement('h5');
        title.textContent = 'Upcoming Deadlines';
        title.className = 'mb-3 fw-bold';

        const list = document.createElement('div');
        list.className = 'vstack gap-3';

        if (assignments.length === 0) {
            list.innerHTML = '<div class="text-center py-3 text-muted">No upcoming assignments</div>';
        }

        assignments.forEach((a, idx) => {
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-3 p-3 rounded-4 border bg-white shadow-sm hover-lift animate-in';
            row.classList.add(`stagger-${(idx % 5) + 1}`);

            const icon = document.createElement('div');
            icon.className = 'avatar-sm bg-primary-subtle rounded-circle text-primary d-flex align-items-center justify-content-center';
            icon.style.minWidth = '42px';
            icon.style.height = '42px';
            icon.innerHTML = '<i class="bi bi-journal-text fs-5"></i>';

            const info = document.createElement('div');
            info.className = 'flex-fill';

            const name = document.createElement('div');
            name.className = 'fw-bold text-dark';
            name.textContent = a.title;

            const date = document.createElement('div');
            date.className = 'small text-muted';
            date.textContent = a.due_date ? new Date(a.due_date).toLocaleDateString() : 'N/A';

            info.append(name, date);
            row.append(icon, info);
            list.appendChild(row);
        });

        wrapper.append(title, list);
        root.appendChild(wrapper);
    };

    /* ================= TABLE ================= */

    const renderTable = async (url, tbodyId, entityType) => {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        // --- Request Coordination (Anti-Race Condition) ---
        if (State.controllers.table) {
            State.controllers.table.abort();
        }
        State.controllers.table = new AbortController();
        const { signal } = State.controllers.table;

        tbody.replaceChildren();

        const loadingRow = document.createElement('tr');
        loadingRow.className = 'shimmer-row'; // Use new CSS shimmer
        loadingRow.innerHTML = `<td colspan="10" class="text-center py-5">
            <div class="spinner-border text-primary-premium spinner-border-sm" role="status"></div>
            <span class="ms-2 text-muted">Synchronizing data...</span>
        </td>`;
        tbody.appendChild(loadingRow);

        try {
            await fetchUser();
            if (signal.aborted) return;

            const studentId = State.currentStudentId;
            let scopedUrl = url;

            // Track state for sorting re-runs
            State.table.lastUrl = url;
            State.table.tbodyId = tbodyId;
            State.table.entityType = entityType;

            const separator = scopedUrl.includes('?') ? '&' : '?';

            if (studentId) {
                scopedUrl += `${separator}student_id=${studentId}`;
            }

            // Append sorting params
            if (State.table.sortField) {
                const sortSep = scopedUrl.includes('?') ? '&' : '?';
                scopedUrl += `${sortSep}sort_by=${State.table.sortField}&sort_order=${State.table.sortOrder}`;
            }

            const res = await axios.get(scopedUrl, { signal });
            const items = res?.data?.data;

            if (signal.aborted) return;

            tbody.replaceChildren();

            if (!Array.isArray(items) || items.length === 0) {
                tbody.appendChild(emptyRow('No records found'));
                return;
            }

            const headerCells = tbody.closest('table').querySelectorAll('thead th');
            const labels = Array.from(headerCells).map(th => th.innerText.trim());

            const fragment = document.createDocumentFragment();
            const isCallback = typeof entityType === 'function';

            items.forEach((item, idx) => {
                let row;
                if (isCallback) {
                    const rowHtml = entityType(item);
                    const template = document.createElement('template');
                    template.innerHTML = rowHtml.trim();
                    row = template.content.firstChild;
                } else {
                    row = renderTableRow(item, entityType, labels);
                }

                if (row) {
                    row.classList.add('animate-in');
                    row.classList.add(`stagger-${(idx % 5) + 1}`);
                    fragment.appendChild(row);
                }
            });
            tbody.appendChild(fragment);
            tbody.appendChild(fragment);
            updateSortHeaders(tbodyId);
            enforceSessionLock();

            res.data = null;
        } catch (err) {
            if (axios.isCancel(err) || err.name === 'AbortError') {
                return; // Silently handle intended cancellations
            }
            console.error('Table render error:', err);
            tbody.replaceChildren();

            let message = 'Error loading data';
            if (err.response?.status === 403) {
                message = '<i class="bi bi-shield-lock me-1"></i> Access Denied';
            } else if (err.response?.status === 404) {
                message = '<i class="bi bi-exclamation-circle me-1"></i> Resource Not Found';
            }

            tbody.appendChild(emptyRow(message, true));
        } finally {
            if (State.controllers.table?.signal === signal) {
                State.controllers.table = null;
            }
        }
    };

    const handleSortClicks = (e) => {
        const th = e.target.closest('th.sortable-header');
        if (!th) return;

        const field = th.dataset.sort;
        if (!field) return;

        if (State.table.sortField === field) {
            State.table.sortOrder = State.table.sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            State.table.sortField = field;
            State.table.sortOrder = 'asc';
        }

        if (State.table.lastUrl) {
            renderTable(State.table.lastUrl, State.table.tbodyId, State.table.entityType);
        }
    };

    const updateSortHeaders = (tbodyId) => {
        const table = document.getElementById(tbodyId)?.closest('table');
        if (!table) return;

        table.querySelectorAll('th.sortable-header').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
            if (th.dataset.sort === State.table.sortField) {
                th.classList.add(`sort-${State.table.sortOrder}`);
            }
        });
    };

    const renderTableRow = (item, type, labels = []) => {
        const tr = document.createElement('tr');
        const user = State.user || window.AppConfig?.user;

        // Robust role detection: handle spaces, underscores, and case-insensitivity
        const roles = (user?.roles || []).map(r => String(r).toLowerCase().replace(/\s+/g, '_'));
        const isSuperAdmin = roles.includes('super_admin') || roles.includes('admin') || roles.includes('owner');

        if (DEBUG && type === 'school') {
            console.log(`App: Rendering school row [${item.id}]. Roles:`, roles, 'SuperAdmin:', isSuperAdmin);
        }

        const appendTd = (el, index) => {
            if (labels[index]) el.dataset.label = labels[index];
            tr.appendChild(el);
        };
        // if (type === 'subject') {
        //     const nameTd = document.createElement('td');
        //     nameTd.innerHTML = `<strong>${escapeText(item.name || 'N/A')}</strong>`;
        //     appendTd(nameTd, 0); // append to first column

        //     const codeTd = document.createElement('td');
        //     codeTd.textContent = item.code || 'N/A';
        //     appendTd(codeTd, 1); // append to second column

        //     // Actions column (edit/delete) should be added here if needed
        // }


        if (type === 'assignment') {
            const titleTd = document.createElement('td');
            titleTd.textContent = item.title;
            tr.appendChild(titleTd);

            const classTd = document.createElement('td');
            classTd.textContent = item.class_room?.name || 'N/A';
            tr.appendChild(classTd);

            const subjectTd = document.createElement('td');
            subjectTd.textContent = item.subject?.name || 'N/A';
            tr.appendChild(subjectTd);

            const dateTd = document.createElement('td');
            dateTd.textContent = new Date(item.due_date).toLocaleDateString();
            tr.appendChild(dateTd);

            const statusTd = document.createElement('td');
            const isActive = item.status === 'active';
            statusTd.innerHTML = `<span class="badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2">${item.status}</span>`;
            tr.appendChild(statusTd);

            const actionTd = document.createElement('td');
            actionTd.className = 'text-end';

            if (roles.includes('student')) {
                const submitBtn = document.createElement('button');
                submitBtn.className = 'btn btn-sm btn-primary-premium ms-1';
                submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit';
                submitBtn.onclick = () => openSubmissionModal(item);
                actionTd.appendChild(submitBtn);

                if (isSuperAdmin || roles.includes('teacher') || roles.includes('school_admin')) {
                    actionTd.append(
                        actionButton('<i class="bi bi-pencil-square"></i>', 'edit', type, item.id),
                        actionButton('<i class="bi bi-trash"></i>', 'delete', type, item.id)
                    );
                }
            } else {
                if (canPerformAction(type, 'edit', roles, isSuperAdmin)) {
                    actionTd.appendChild(actionButton('<i class="bi bi-pencil-square"></i>', 'edit', type, item.id));
                }
                if (canPerformAction(type, 'delete', roles, isSuperAdmin)) {
                    actionTd.appendChild(actionButton('<i class="bi bi-trash"></i>', 'delete', type, item.id));
                }
            }
            appendTd(actionTd, labels.length - 1);
            return tr;
        }

        if (type === 'student') {
            const studentTd = document.createElement('td');
            studentTd.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-2 bg-light rounded text-center" style="width:32px; height:32px; line-height:32px;">
                        <i class="bi bi-person text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">${escapeText(item.full_name || item.name)}</div>
                        <div class="small text-muted">${escapeText(item.user?.email || item.email || '')}</div>
                    </div>
                </div>
            `;
            appendTd(studentTd, 0);

            const admissionTd = document.createElement('td');
            admissionTd.innerHTML = `<span class="badge bg-light text-dark border">${escapeText(item.admission_number)}</span>`;
            appendTd(admissionTd, 1);

            const classTd = document.createElement('td');
            classTd.textContent = item.current_class || item.class_room?.name || 'N/A';
            appendTd(classTd, 2);

            const genderTd = document.createElement('td');
            genderTd.innerHTML = `<span class="text-capitalize">${escapeText(item.gender || item.user?.gender || 'N/A')}</span>`;
            appendTd(genderTd, 3);

            const statusTd = document.createElement('td');
            const isActive = item.status === true || item.status === 1 || item.status === 'active';
            statusTd.innerHTML = `<span class="badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2">
                ${isActive ? 'Active' : 'Inactive'}
            </span>`;
            appendTd(statusTd, 4);
        } else if (type === 'school') {
            const nameTd = document.createElement('td');
            nameTd.innerHTML = safeHTML`
                <div class="fw-bold text-dark">${item.name}</div>
                <div class="small text-muted">${item.email || ''}</div>
                <div class="small text-muted">${item.phone || ''}</div>
            `;
            appendTd(nameTd, 0);

            const locTd = document.createElement('td');
            locTd.innerHTML = safeHTML`
                <div class="small text-wrap" style="max-width: 200px;">${item.address || ''}</div>
                <div class="small text-muted">${item.city || ''}, ${item.state || ''}</div>
            `;
            appendTd(locTd, 1);

            const personTd = document.createElement('td');
            personTd.innerHTML = safeHTML`
                <div>${item.contact_person || 'N/A'}</div>
                <div class="small text-muted">${item.contact_person_phone || ''}</div>
            `;
            appendTd(personTd, 2);

            const statsTd = document.createElement('td');
            statsTd.innerHTML = `
                <span class="badge bg-light text-dark border me-1"><i class="bi bi-people"></i> ${item.users_count || 0}</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-mortarboard"></i> ${item.students_count || 0}</span>
            `;
            appendTd(statsTd, 3);

            const planTd = document.createElement('td');
            planTd.innerHTML = `<span class="badge bg-info-subtle text-info text-uppercase">${item.plan || 'Basic'}</span>`;
            appendTd(planTd, 4);

            const statusTd = document.createElement('td');
            const isActive = item.is_active || item.status === 'active';
            statusTd.innerHTML = `<span class="badge bg-${isActive ? 'success' : 'danger'}-subtle text-${isActive ? 'success' : 'danger'} px-2">
                ${item.status_label || (isActive ? 'Active' : 'Inactive')}
            </span>`;
            appendTd(statusTd, 5);

        } else if (type === 'teacher') {
            const avatarUrl = item.user?.profile_photo_url || '';
            const teacherTd = document.createElement('td');
            teacherTd.innerHTML = safeHTML`
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                ${trust(avatarUrl ?
                safeHTML`<img src="${avatarUrl}" class="rounded-circle" style="width:38px; height:38px; object-fit:cover;" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(item.user?.name || 'T')}&color=7F9CF5&background=EBF4FF'">` :
                safeHTML`<div class="avatar-sm bg-primary-subtle rounded-circle text-primary d-flex align-items-center justify-content-center" style="width:38px; height:38px;">
                                <i class="bi bi-person-workspace"></i>
                            </div>`
            )}
                    </div>
                    <div>
                        <div class="fw-bold text-dark">${item.user?.name || item.name || 'N/A'}</div>
                        <div class="small text-muted text-lowercase">${item.user?.email || item.email || ''}</div>
                    </div>
                </div>
            `;
            appendTd(teacherTd, 0);

            const employeeTd = document.createElement('td');
            employeeTd.innerHTML = `<span class="badge bg-light text-dark border">${escapeText(item.employee_number || 'N/A')}</span>`;
            appendTd(employeeTd, 1);

            const statusTd = document.createElement('td');
            const isActive = item.deleted_at === null && (item.status === 'active' || item.status === true || item.status === 1 || item.status === undefined || item.status === null);
            statusTd.innerHTML = `<span class="badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2">
                ${isActive ? 'Active' : 'Inactive'}
            </span>`;
            appendTd(statusTd, 2);

            const phoneTd = document.createElement('td');
            phoneTd.className = 'text-muted small';
            phoneTd.textContent = item.user?.phone || 'N/A';
            appendTd(phoneTd, 3);

            const hireDateTd = document.createElement('td');
            hireDateTd.className = 'text-muted small';
            hireDateTd.textContent = item.hire_date ? new Date(item.hire_date).toLocaleDateString() : 'N/A';
            appendTd(hireDateTd, 4);
        } else if (type === 'attendance') {
            const studentTd = document.createElement('td');
            studentTd.textContent = item.student?.user?.name || item.student?.full_name || 'N/A';
            appendTd(studentTd, 0);

            const presentTd = document.createElement('td');
            presentTd.className = 'text-center';
            presentTd.innerHTML = `<input type="radio" name="attendance[${item.id}][status]" value="present" ${item.status === 'present' ? 'checked' : ''}>`;
            appendTd(presentTd, 1);

            const lateTd = document.createElement('td');
            lateTd.className = 'text-center';
            lateTd.innerHTML = `<input type="radio" name="attendance[${item.id}][status]" value="late" ${item.status === 'late' ? 'checked' : ''}>`;
            appendTd(lateTd, 2);

            const absentTd = document.createElement('td');
            absentTd.className = 'text-center';
            absentTd.innerHTML = `<input type="radio" name="attendance[${item.id}][status]" value="absent" ${item.status === 'absent' ? 'checked' : ''}>`;
            appendTd(absentTd, 3);

            const noteTd = document.createElement('td');
            noteTd.innerHTML = `<input type="text" name="attendance[${item.id}][remark]" class="form-control form-control-sm" value="${item.remark || ''}">`;
            appendTd(noteTd, 4);

            return tr; // Attendance has its own actions in footer
        } else if (type === 'payment' || type === 'fee-payment') {
            const studentTd = document.createElement('td');
            studentTd.textContent = item.student?.user?.name || item.student?.full_name || 'N/A';
            appendTd(studentTd, 0);

            const amountTd = document.createElement('td');
            amountTd.textContent = item.amount || '0';
            appendTd(amountTd, 1);

            const dateTd = document.createElement('td');
            dateTd.textContent = item.payment_date ? new Date(item.payment_date).toLocaleDateString() : 'N/A';
            appendTd(dateTd, 2);

            const methodTd = document.createElement('td');
            methodTd.textContent = item.payment_method || 'N/A';
            appendTd(methodTd, 3);

            const statusTd = document.createElement('td');
            statusTd.innerHTML = `<span class="badge bg-${item.status === 'paid' ? 'success' : 'warning'}-subtle text-${item.status === 'paid' ? 'success' : 'warning'} px-2">${item.status}</span>`;
            appendTd(statusTd, 4);
        } else if (type === 'class' || type === 'subject' || type === 'session' || type === 'section' || type === 'feeType' || type === 'fee-type') {
            const nameTd = document.createElement('td');
            nameTd.innerHTML = `<strong>${item.name || item.title || 'N/A'}</strong>`;
            appendTd(nameTd, 0);

            if (type === 'class') {
                const teacherTd = document.createElement('td');
                teacherTd.textContent = item.class_teacher?.user?.name || 'N/A';
                appendTd(teacherTd, 1);

                const studentsTd = document.createElement('td');
                studentsTd.innerHTML = `<span class="badge bg-primary-subtle text-primary border">${item.students_count || 0}</span>`;
                appendTd(studentsTd, 2);

                const subjectsTd = document.createElement('td');
                subjectsTd.innerHTML = `<span class="badge bg-info-subtle text-info border">${item.subjects_count || 0}</span>`;
                appendTd(subjectsTd, 3);

                const assignmentsTd = document.createElement('td');
                assignmentsTd.innerHTML = `<span class="badge bg-warning-subtle text-warning border">${item.assignments_count || 0}</span>`;
                appendTd(assignmentsTd, 4);
            } else if (type === 'feeType' || type === 'fee-type') {
                const amountTd = document.createElement('td');
                amountTd.textContent = item.amount || '0';
                appendTd(amountTd, 1);
            }
        } else if (type === 'lessonNote') {
            const titleTd = document.createElement('td');
            titleTd.innerHTML = `<strong>${escapeText(item.title)}</strong>`;
            appendTd(titleTd, 0);

            const classTd = document.createElement('td');
            classTd.textContent = item.class_room?.name || 'N/A';
            appendTd(classTd, 1);

            const subjectTd = document.createElement('td');
            subjectTd.textContent = item.subject?.name || 'N/A';
            appendTd(subjectTd, 2);

            const dateTd = document.createElement('td');
            dateTd.textContent = item.date ? new Date(item.date).toLocaleDateString() : 'N/A';
            appendTd(dateTd, 3);
        } else if (type === 'guardian') {
            // Guardian column: Name and Email
            const guardianTd = document.createElement('td');
            guardianTd.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-2 bg-light rounded text-center" style="width:32px; height:32px; line-height:32px;">
                        <i class="bi bi-person-check text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">${escapeText(item.user?.name || item.name || 'N/A')}</div>
                        <div class="small text-muted">${escapeText(item.user?.email || item.email || '')}</div>
                    </div>
                </div>
            `;
            appendTd(guardianTd, 0);

            // Phone / Occupation column
            const contactTd = document.createElement('td');
            contactTd.innerHTML = `
                <div>${escapeText(item.user?.phone || 'N/A')}</div>
                <div class="small text-muted">${escapeText(item.occupation || '')}</div>
            `;
            appendTd(contactTd, 1);

            // Relation column
            const relationTd = document.createElement('td');
            relationTd.innerHTML = `<span class="badge bg-info-subtle text-info text-capitalize px-2">${escapeText(item.relation || 'N/A')}</span>`;
            appendTd(relationTd, 2);
        } else {
            const nameTd = document.createElement('td');
            nameTd.textContent = item.name || item.title || 'N/A';
            appendTd(nameTd, 0);
        }

        const actionTd = document.createElement('td');
        actionTd.className = 'text-end';

        if (type === 'school' && !item.is_active && isSuperAdmin) {
            const approveBtn = document.createElement('button');
            approveBtn.type = 'button';
            approveBtn.className = 'btn btn-sm btn-success-subtle text-success me-1';
            approveBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
            approveBtn.title = 'Approve School';
            approveBtn.onclick = () => window.approveSchool ? window.approveSchool(item.id) : console.warn('approveSchool fn missing');
            actionTd.appendChild(approveBtn);
        }

        if (canPerformAction(type, 'edit', roles, isSuperAdmin)) {
            actionTd.appendChild(actionButton('<i class="bi bi-pencil-square"></i>', 'edit', type, item.id));
        }
        if (canPerformAction(type, 'delete', roles, isSuperAdmin)) {
            actionTd.appendChild(actionButton('<i class="bi bi-trash"></i>', 'delete', type, item.id));
        }

        appendTd(actionTd, labels.length - 1);
        return tr;
    };

    const canPerformAction = (entity, action, roles, isSuperAdmin) => {
        if (isSuperAdmin) return true;

        const permissionMap = {
            'student': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'teacher': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'guardian': {
                'edit': ['school_admin', 'teacher'],
                'delete': ['school_admin']
            },
            'class': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'subject': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },

            'session': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'term': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'section': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'enrollment': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'assignment': {
                'edit': ['school_admin', 'teacher'],
                'delete': ['school_admin', 'teacher']
            },
            'lessonNote': {
                'edit': ['school_admin', 'teacher'],
                'delete': ['school_admin', 'teacher']
            },
            'assessment': {
                'edit': ['school_admin', 'teacher'],
                'delete': ['school_admin', 'teacher']
            },
            'result': {
                'edit': ['school_admin', 'teacher'],
                'delete': ['school_admin', 'teacher']
            },
            'payment': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'feeType': {
                'edit': ['school_admin'],
                'delete': ['school_admin']
            },
            'school': {
                'edit': ['super_admin'],
                'delete': ['super_admin']
            }
        };

        const allowedRoles = permissionMap[entity]?.[action];
        if (!allowedRoles) return false;

        return roles.some(r => allowedRoles.includes(r));
    };

    const actionButton = (html, action, entity, id) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `btn btn-sm btn-outline-${action === 'delete' ? 'danger' : 'primary'} ms-1`;
        btn.innerHTML = html;
        btn.dataset.action = action;
        btn.dataset.entity = entity;
        btn.dataset.id = id;
        return btn;
    };

    const emptyRow = (message, isError = false) => {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 10;
        td.className = `text-center py-4 ${isError ? 'text-danger' : 'text-muted'}`;
        td.innerHTML = message;
        tr.appendChild(td);
        return tr;
    };

    /* ================= EVENTS ================= */

    const handleActionClicks = async (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;

        const { action, entity, id } = btn.dataset;
        const endpoint = getApiEndpoint(entity);

        if (action === 'delete') {
            const reloadFn = window[`reload${capitalize(entity)}s`] || window[`reload${capitalize(entity)}`];
            deleteItem(`/api/v1/${endpoint}/${id}`, reloadFn);
        }

        if (action === 'edit') {
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                // Fetch full data for the entity before editing
                const res = await axios.get(`/api/v1/${endpoint}/${id}`);
                const data = res?.data?.data || res?.data;

                const fn = window[`edit${capitalize(entity)}`];
                if (typeof fn === 'function') {
                    fn(data);
                } else {
                    console.error(`Global function edit${capitalize(entity)} not found.`);
                }

                // Release response data to help garbage collection
                res.data = null;
            } catch (err) {
                console.error(`Failed to fetch ${entity} data:`, err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load item data.' });
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    };

    const getApiEndpoint = (entity) => {
        const map = {
            'class': 'classes',
            'library': 'library/books',
            'session': 'school-sessions',
            'feeType': 'fee-types',
            'invoiceItem': 'invoice-items',
            'lessonNote': 'lesson-notes',
            'assignmentSubmission': 'assignment-submissions',
            'fee-type': 'fee-types', // cover both cases
        };

        if (map[entity]) return map[entity];

        // Default: append 's'
        return `${entity}s`;
    };

    /* ================= FORMS ================= */

    const submitForm = async (event, callback, entityType, modalId) => {
        event.preventDefault();

        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHtml = submitBtn?.innerHTML;

        const data = new FormData(form);

        clearFormErrors(form);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        }

        try {
            const res = await axios({
                method: form.method,
                url: form.action,
                data
            });

            closeModal(modalId);
            form.reset();

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: res.data.message || 'Action completed successfully',
                timer: 2000,
                showConfirmButton: false
            });

            callback ? callback(res.data) : location.reload();
        } catch (err) {
            if (err.response?.status === 422) {
                showFormErrors(form, err.response.data.errors);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: err.response?.data?.message || 'Something went wrong!'
                });
            }
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    };

    const clearFormErrors = (form) => {
        form.querySelectorAll('.is-invalid').forEach(i => i.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(e => e.remove());
    };

    const showFormErrors = (form, errors) => {
        Object.entries(errors).forEach(([field, msgs]) => {
            const input = form.elements[field];
            if (!input) return;

            input.classList.add('is-invalid');

            const div = document.createElement('div');
            div.className = 'invalid-feedback';
            div.textContent = msgs[0];
            input.after(div);
        });
    };

    const populateForm = (form, data, prefix = '') => {
        if (!data) return;

        Object.entries(data).forEach(([key, value]) => {
            const fieldName = prefix ? `${prefix}[${key}]` : key;

            if (value !== null && typeof value === 'object' && !Array.isArray(value)) {
                populateForm(form, value, fieldName);
                populateForm(form, value, '');
            } else {
                const element = form.elements[fieldName] || form.elements[key];
                if (element) {
                    if (element.type === 'checkbox') {
                        element.checked = !!value;
                    } else if (element.type === 'radio') {
                        const radio = form.querySelector(`input[name="${fieldName}"][value="${value}"]`);
                        if (radio) radio.checked = true;
                    } else {
                        element.value = value ?? '';
                    }
                }
            }
        });
    };

    /* ================= HELPERS ================= */

    const deleteItem = async (url, callback = null) => {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(url);
                await Swal.fire({
                    title: 'Deleted!',
                    text: 'Your item has been deleted.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                if (callback && typeof callback === 'function') {
                    callback();
                } else {
                    location.reload();
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Failed to delete item' });
            }
        }
    };

    const closeModal = (id) => {
        if (!id) return;
        const el = document.getElementById(id);
        if (el) bootstrap.Modal.getInstance(el)?.hide();
    };

    const logout = async () => {
        console.log('App: Logout initiated');
        try {
            await axios.post('/logout');
            Swal.fire({
                icon: 'success',
                title: 'Logged Out',
                text: 'You have been successfully logged out.',
                timer: 1000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        } catch (e) {
            console.warn('App: Logout API failed or was unauthorized', e);
        } finally {
            console.log('App: Cleaning up storage and redirecting');
            // Clear all client-side storage
            localStorage.clear();
            sessionStorage.clear();

            // Use replace to prevent back button issues
            setTimeout(() => {
                window.location.replace('/');
            }, 800);
        }
    };

    const loadOptions = async (url, elementId, selectedId = null, valueKey = 'id', labelKey = 'name', placeholder = 'Select option') => {
        const select = document.getElementById(elementId);
        if (!select) return;

        select.replaceChildren();
        const loadingOpt = document.createElement('option');
        loadingOpt.textContent = 'Loading options...';
        select.appendChild(loadingOpt);
        select.disabled = true;

        try {
            const res = await axios.get(url);
            const data = res?.data?.data || res?.data || [];

            select.replaceChildren(); // Clear loading

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = placeholder;
            select.appendChild(defaultOpt);

            data.forEach(item => {
                const opt = document.createElement('option');
                // Handle valueKey (simple property)
                opt.value = item[valueKey];

                // Handle labelKey (string property or callback function)
                if (typeof labelKey === 'function') {
                    opt.textContent = labelKey(item);
                } else {
                    opt.textContent = item[labelKey] || 'N/A';
                }

                if (selectedId && String(item[valueKey]) === String(selectedId)) opt.selected = true;
                select.appendChild(opt);
            });

            select.disabled = false;
        } catch (err) {
            console.error('loadOptions error:', err);
            select.replaceChildren();
            const opt = document.createElement('option');
            opt.textContent = 'Failed to load';
            select.appendChild(opt);
        }
    };

    const formatCurrency = (val) =>
        new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val || 0);

    const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);

    // Create the App object with property getters for legacy compatibility
    const AppInstance = {
        init,
        renderTable,
        submitForm,
        populateForm,
        deleteItem,
        logout,
        loadOptions,
        renderStudentSelector,
        selectStudent,
        resetForm,
        safeHTML,
        trust,
        escapeText,
        get user() { return State.user; },
        set user(val) { State.user = val; },
        get currentStudentId() { return State.currentStudentId; },
        set currentStudentId(val) { State.currentStudentId = val; },
        // Extended exports
        renderPlatformStats,
        enforceSessionLock,
        loadGradingOptions,
        get: (url, callback) => {
            axios.get(url)
                .then(res => callback(res.data))
                .catch(err => {
                    console.error('App.get error:', err);
                    Swal.fire('Error', 'Failed to fetch data', 'error');
                });
        }
    };

    window.App = AppInstance;
    return AppInstance;
})();
// Initialize application
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // console.log('App: DOM Loaded, initializing components...');
        const sidebar = new SidebarManager();
        await sidebar.init();
        if (window.App && typeof window.App.init === 'function') {
            window.App.init();
        }
    } catch (err) {
        console.error('App: Critical initialization failure', err);
    }
});
