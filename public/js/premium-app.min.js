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
        }
    };

    // Run immediately
    setupAxios();

    const init = () => {
        document.addEventListener('click', handleActionClicks);

        if (document.getElementById('dashboard-stats-root')) {
            loadDashboard();
        }
    };

    const escapeText = (val) => {
        return String(val ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const safeHTML = (strings, ...values) => {
        // Hardened: We no longer trust any string value as markup starting with '<'
        // Every dynamic value is escaped unless it's an instance of an HTMLElement we created ourselves
        const html = strings.reduce((acc, str, i) => {
            const val = values[i] !== undefined ? values[i] : '';
            let escaped;
            if (val instanceof HTMLElement) {
                escaped = val.outerHTML;
            } else {
                escaped = escapeText(val);
            }
            return acc + str + escaped;
        }, '');
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        return template.content.firstChild;
    };

    /* ================= DASHBOARD ================= */

    const parallelLoad = async (configs) => {
        const entries = Object.entries(configs);
        const results = await Promise.allSettled(entries.map(([_, fn]) => fn()));
        const data = {};
        results.forEach((res, i) => {
            data[entries[i][0]] = res.status === 'fulfilled' ? res.value : null;
        });
        return data;
    };

    const loadDashboard = async () => {
        const root = document.getElementById('dashboard-stats-root');
        if (!root) return;

        // Show individual skeletons
        root.replaceChildren();
        for (let i = 0; i < 6; i++) {
            root.appendChild(createStatSkeleton());
        }

        try {
            // Parallel load stats and activity if they are separate endpoints (currently one)
            // But we can still simulate parallelism for future-proofing or if we split them
            const res = await axios.get('/api/v1/dashboard/stats');
            if (!res?.data?.data) throw new Error();

            const data = res.data.data;

            // Staggered fade-in effect: Clear skeletons and render real cards
            // Use requestAnimationFrame for smooth transition
            requestAnimationFrame(() => {
                root.replaceChildren();
                if (data.platform) {
                    renderPlatformStats(root, data.platform);
                } else if (data.student) {
                    renderStudentStats(root, data.student);
                } else if (data.teacher) {
                    renderTeacherStats(root, data.teacher);
                } else if (data.general) {
                    renderSchoolStats(root, data);
                }

                // Render Charts if they exist
                if (data.charts) {
                    renderCharts(data.charts);
                }
            });

        } catch (err) {
            console.error('Dashboard load error:', err);
            root.replaceChildren();
            const errorDiv = document.createElement('div');
            errorDiv.className = 'col-12 text-center py-5 text-danger';
            errorDiv.textContent = 'Failed to load dashboard data. Please try refreshing.';
            root.appendChild(errorDiv);
        }
    };

    const renderPlatformStats = (root, stats) => {
        const cards = [
            ['Total Schools', stats.total_schools, 'bi-building'],
            ['Active Schools', stats.active_schools, 'bi-check-circle-fill'],
            ['Total Users', stats.total_users, 'bi-people'],
            ['Total Students', stats.total_students, 'bi-mortarboard'],
            ['Total Teachers', stats.total_teachers, 'bi-person-badge'],
            ['Platform Revenue', formatCurrency(stats.total_revenue), 'bi-cash-stack']
        ];

        cards.forEach(([label, value, icon]) => {
            root.appendChild(createStatCard(label, value, icon));
        });
    };

    const renderStudentStats = (root, stats) => {
        root.appendChild(createStatCard('Attendance Rate', `${Math.round(stats.attendance)}%`, 'bi-calendar-check'));
        root.appendChild(createStatCard('Active Assignments', stats.assignments, 'bi-journal-text'));
        root.appendChild(createStatCard('Average Grade', stats.avg_marks.toFixed(1), 'bi-award'));

        const activityRoot = document.getElementById('dashboard-activity-root');
        if (activityRoot && Array.isArray(stats.upcoming_assignments)) {
            renderActivity(activityRoot, stats.upcoming_assignments);
        }
    };

    const renderTeacherStats = (root, stats) => {
        root.appendChild(createStatCard('Total Classes', stats.classes, 'bi-grid'));
        root.appendChild(createStatCard('Total Students', stats.students, 'bi-people'));
        root.appendChild(createStatCard('Pending Grading', stats.assignments, 'bi-clipboard-check'));

        const activityRoot = document.getElementById('dashboard-activity-root');
        if (activityRoot && stats.academic?.upcoming_assignments) {
            renderActivity(activityRoot, stats.academic.upcoming_assignments);
        }
    };

    const renderSchoolStats = (root, stats) => {
        // Row 1: Academic Overview
        root.appendChild(createStatCard('Total Students', stats.general?.students || 0, 'bi-people'));
        root.appendChild(createStatCard('Active Teachers', stats.general?.teachers || 0, 'bi-person-badge'));
        root.appendChild(createStatCard('Total Classes', stats.general?.classes || 0, 'bi-building'));

        // Row 2: Engagement & Billing
        root.appendChild(createStatCard('Assignments', stats.general?.assignments || 0, 'bi-journal-text'));
        root.appendChild(createStatCard('Attendance Today', stats.general?.attendance_today || 0, 'bi-calendar-check'));
        root.appendChild(createStatCard('Recent Reg (7d)', stats.general?.recent_registrations || 0, 'bi-person-plus'));

        // Row 3: Financial Health
        root.appendChild(createStatCard('Total Revenue', formatCurrency(stats.finance?.payments?.total_amount || 0), 'bi-cash-stack'));
        root.appendChild(createStatCard('Outstanding Balance', formatCurrency(stats.finance?.outstanding_balance || 0), 'bi-piggy-bank'));
        root.appendChild(createStatCard('Collectable Fees', formatCurrency(stats.general?.collectable_fees || 0), 'bi-wallet2'));

        const activityRoot = document.getElementById('dashboard-activity-root');
        if (activityRoot && Array.isArray(stats.academic?.upcoming_assignments)) {
            renderActivity(activityRoot, stats.academic.upcoming_assignments);
        }
    };

    const createStatCard = (label, value, icon = 'bi-graph-up') => {
        const col = document.createElement('div');
        col.className = 'col-md-4 mb-4 animate-in';

        const card = document.createElement('div');
        card.className = 'card-premium h-100 p-3 d-flex align-items-center gap-3 transition-all-premium';

        const iconBox = document.createElement('div');
        iconBox.className = 'avatar-md bg-primary-subtle rounded-circle text-primary d-flex align-items-center justify-content-center flex-shrink-0';
        iconBox.style.width = '48px';
        iconBox.style.height = '48px';
        iconBox.innerHTML = `<i class="bi ${icon} fs-4"></i>`;

        const content = document.createElement('div');
        content.className = 'overflow-hidden';

        const p = document.createElement('p');
        p.className = 'text-muted text-uppercase small mb-1 text-truncate';
        p.textContent = label;

        const h = document.createElement('h3');
        h.className = 'h3 mb-0 text-truncate';
        h.textContent = value;

        content.append(p, h);
        card.append(iconBox, content);
        col.appendChild(card);

        return col;
    };

    const createStatSkeleton = () => {
        const col = document.createElement('div');
        col.className = 'col-md-4 mb-4';

        const card = document.createElement('div');
        card.className = 'card-premium h-100 p-3 d-flex align-items-center gap-3';

        const icon = document.createElement('div');
        icon.className = 'sidebar-skeleton-icon';
        icon.style.cssText = 'width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0;';

        const content = document.createElement('div');
        content.className = 'flex-grow-1';

        const t1 = document.createElement('div');
        t1.className = 'sidebar-skeleton-text';
        t1.style.width = '60%';
        t1.style.marginBottom = '8px';

        const t2 = document.createElement('div');
        t2.className = 'sidebar-skeleton-text';
        t2.style.width = '40%';

        content.append(t1, t2);
        card.append(icon, content);
        col.appendChild(card);
        return col;
    };

    const renderCharts = (charts) => {
        const root = document.getElementById('dashboard-charts-root');
        if (!root) return;

        root.replaceChildren();
        const entries = Object.entries(charts);
        const queue = [];

        entries.forEach(([key, config]) => {
            if (!config || !config.labels || !Array.isArray(config.data)) return;

            const colSize = config.labels.length > 7 ? 'col-12' : 'col-md-6';
            const col = document.createElement('div');
            col.className = `${colSize} mb-4 animate-in`;

            const card = document.createElement('div');
            card.className = 'card-premium p-4 h-100 shadow-sm border-0';

            const title = document.createElement('h6');
            title.className = 'fw-bold mb-4 text-muted text-uppercase small';
            title.textContent = key.replace(/_/g, ' ');

            const canvasWrapper = document.createElement('div');
            canvasWrapper.style.height = '300px';
            canvasWrapper.className = 'position-relative';

            const canvas = document.createElement('canvas');
            canvasWrapper.appendChild(canvas);
            card.append(title, canvasWrapper);
            col.appendChild(card);
            root.appendChild(col);

            // Add to initialization queue
            queue.push({ canvas, key, config });
        });

        // Staggered initialization
        const processQueue = () => {
            if (queue.length === 0) return;
            const task = queue.shift();
            initChart(task.canvas, task.key, task.config);
            requestAnimationFrame(processQueue);
        };

        requestAnimationFrame(processQueue);
    };

    const initChart = (canvas, type, config) => {
        if (!window.Chart) return;

        const colors = {
            primary: '#4f46e5', // Deep Indigo
            secondary: '#94a3b8', // Slate
            success: '#10b981', // Emerald
            warning: '#f59e0b', // Amber
            danger: '#ef4444', // Red
            info: '#06b6d4', // Cyan
            palette: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#6366f1']
        };

        const typeMap = {
            'distribution': 'doughnut',
            'status': 'doughnut',
            'demographics': 'doughnut',
            'engagement': 'doughnut',
            'growth': 'line',
            'trends': 'line',
            'flow': 'line',
            'pulse': 'bar',
            'performance': 'bar',
            'by_class': 'bar'
        };

        let chartType = 'line';
        for (const [key, val] of Object.entries(typeMap)) {
            if (type.toLowerCase().includes(key)) {
                chartType = val;
                break;
            }
        }

        const isDoughnut = chartType === 'doughnut';
        const isBar = chartType === 'bar';

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: chartType,
            data: {
                labels: config.labels,
                datasets: [{
                    label: type.replace(/_/g, ' ').toUpperCase(),
                    data: config.data,
                    backgroundColor: isDoughnut
                        ? colors.palette
                        : (isBar ? colors.primary : 'rgba(79, 70, 229, 0.1)'),
                    borderColor: isDoughnut ? '#ffffff' : colors.primary,
                    borderWidth: isDoughnut ? 2 : 2.5,
                    fill: chartType === 'line',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: colors.primary,
                    pointBorderWidth: 2,
                    borderRadius: isBar ? 8 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: isDoughnut,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Outfit', size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: 'Outfit', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Outfit', size: 12 }
                    }
                },
                scales: isDoughnut ? {} : {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0', drawBorder: false },
                        ticks: { font: { family: 'Outfit', size: 12 }, color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Outfit', size: 11 }, color: '#64748b' }
                    }
                }
            }
        });
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
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'text-center py-3 text-muted';
            emptyDiv.textContent = 'No upcoming assignments';
            list.appendChild(emptyDiv);
        }

        assignments.forEach(a => {
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-3 p-2 rounded-3 border bg-white';

            const icon = document.createElement('div');
            icon.className = 'avatar-sm bg-light rounded text-center d-flex align-items-center justify-content-center';
            icon.style.width = '40px';
            icon.style.height = '40px';
            icon.innerHTML = '<i class="bi bi-file-earmark-text text-primary"></i>';

            const text = document.createElement('div');
            text.className = 'flex-grow-1';

            const name = document.createElement('div');
            name.className = 'fw-semibold';
            name.textContent = a.title;

            const date = document.createElement('div');
            date.className = 'small text-muted';
            date.textContent = `Due: ${new Date(a.due_date).toLocaleDateString()}`;

            text.append(name, date);
            row.append(icon, text);
            list.appendChild(row);
        });

        wrapper.append(title, list);
        root.appendChild(wrapper);
    };

    /* ================= TABLE ================= */

    const activeRenders = new Map();

    const renderTable = async (url, tbodyId, rendererOrType) => {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        // Cancel previous render for this tbody if it exists
        if (activeRenders.has(tbodyId)) {
            activeRenders.set(tbodyId, activeRenders.get(tbodyId) + 1);
        } else {
            activeRenders.set(tbodyId, 1);
        }
        const currentTaskId = activeRenders.get(tbodyId);

        tbody.replaceChildren();
        const skeletonCount = 5;
        for (let i = 0; i < skeletonCount; i++) {
            tbody.appendChild(createSkeletonRow());
        }

        try {
            const res = await axios.get(url);

            // If another render task started, abort this one
            if (activeRenders.get(tbodyId) !== currentTaskId) return;

            let items = res?.data?.data || res?.data;

            // Handle Laravel Pagination: if items is an object with a 'data' array, use that
            if (items && !Array.isArray(items) && Array.isArray(items.data)) {
                items = items.data;
            }

            tbody.replaceChildren();

            if (!Array.isArray(items) || items.length === 0) {
                tbody.appendChild(emptyRow('No records found'));
                return;
            }

            // Chunked rendering to prevent UI freeze and enable smooth entry
            const chunkSize = 8;
            let index = 0;

            const renderNextChunk = () => {
                // If another render task started, abort this one
                if (activeRenders.get(tbodyId) !== currentTaskId) return;
                const limit = Math.min(index + chunkSize, items.length);
                const fragment = document.createDocumentFragment();

                for (; index < limit; index++) {
                    const item = items[index];
                    let row;
                    if (typeof rendererOrType === 'function') {
                        const result = rendererOrType(item);
                        if (result instanceof HTMLElement) {
                            row = result;
                        } else if (typeof result === 'string') {
                            const template = document.createElement('template');
                            template.innerHTML = result.trim();
                            row = template.content.firstChild;
                        }
                    } else {
                        row = renderTableRow(item, rendererOrType);
                    }

                    if (row) {
                        row.classList.add('animate-in');
                        fragment.appendChild(row);
                    }
                }

                tbody.appendChild(fragment);

                if (index < items.length) {
                    requestAnimationFrame(renderNextChunk);
                }
            };

            requestAnimationFrame(renderNextChunk);

        } catch (err) {
            if (activeRenders.get(tbodyId) !== currentTaskId) return;
            console.error('Table render error:', err);
            tbody.replaceChildren();
            tbody.appendChild(emptyRow('Error loading data', true));
        }
    };

    const renderTableRow = (item, type) => {
        const tr = document.createElement('tr');
        // Get role safely
        const userRole = window.AuthUser?.roles?.[0]; // default undefined, checks !== 'student' below

        if (type === 'assignment') {
            // Custom render for assignment to show Submit button for students
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
            const statusBadge = document.createElement('span');
            statusBadge.className = `badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2`;
            statusBadge.textContent = item.status;
            statusTd.appendChild(statusBadge);
            tr.appendChild(statusTd);

            const actionTd = document.createElement('td');
            actionTd.className = 'text-end';

            if (userRole === 'student') {
                // Student Action: Submit
                // Check if already submitted? (Ideally backend sends 'submission_status')
                // For now, just show "Submit"
                const submitBtn = document.createElement('button');
                submitBtn.className = 'btn btn-sm btn-primary-premium ms-1';
                const submitIcon = document.createElement('i');
                submitIcon.className = 'bi bi-send';
                submitBtn.append(submitIcon, document.createTextNode(' Submit'));
                submitBtn.onclick = () => openSubmissionModal(item);
                actionTd.appendChild(submitBtn);
            } else {
                // Teacher/Admin Actions: Edit/Delete
                actionTd.append(
                    actionButton('<i class="bi bi-pencil-square"></i>', 'edit', type, item.id),
                    actionButton('<i class="bi bi-trash"></i>', 'delete', type, item.id)
                );
            }
            tr.appendChild(actionTd);
            return tr;
        }

        if (type === 'student') {
            const studentTd = document.createElement('td');
            const studentDiv = document.createElement('div');
            studentDiv.className = 'd-flex align-items-center';

            const avatarBox = document.createElement('div');
            avatarBox.className = 'avatar-sm me-2 bg-light rounded text-center';
            avatarBox.style.cssText = 'width:32px; height:32px; line-height:32px;';
            avatarBox.innerHTML = '<i class="bi bi-person text-primary"></i>';

            const infoBox = document.createElement('div');
            const nameDiv = document.createElement('div');
            nameDiv.className = 'fw-bold text-dark';
            nameDiv.textContent = item.full_name || item.name || 'N/A';

            const emailDiv = document.createElement('div');
            emailDiv.className = 'small text-muted';
            emailDiv.textContent = item.user?.email || item.email || '';

            infoBox.append(nameDiv, emailDiv);
            studentDiv.append(avatarBox, infoBox);
            studentTd.appendChild(studentDiv);
            tr.appendChild(studentTd);

            const admissionTd = document.createElement('td');
            const admissionBadge = document.createElement('span');
            admissionBadge.className = 'badge bg-light text-dark border';
            admissionBadge.textContent = item.admission_number || 'N/A';
            admissionTd.appendChild(admissionBadge);
            tr.appendChild(admissionTd);

            const gradeTd = document.createElement('td');
            gradeTd.textContent = item.current_grade || item.grade?.name || 'N/A';
            tr.appendChild(gradeTd);

            const genderTd = document.createElement('td');
            const genderSpan = document.createElement('span');
            genderSpan.className = 'text-capitalize';
            genderSpan.textContent = item.gender || item.user?.gender || 'N/A';
            genderTd.appendChild(genderSpan);
            tr.appendChild(genderTd);

            const statusTd = document.createElement('td');
            const isActive = item.status === true || item.status === 1 || item.status === 'active';
            const statusBadge = document.createElement('span');
            statusBadge.className = `badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2`;
            statusBadge.textContent = isActive ? 'Active' : 'Inactive';
            statusTd.appendChild(statusBadge);
            tr.appendChild(statusTd);
        } else if (type === 'class') {
            const nameTd = document.createElement('td');
            nameTd.className = 'fw-bold text-dark';
            nameTd.textContent = item.name;
            tr.appendChild(nameTd);

            const teacherTd = document.createElement('td');
            teacherTd.textContent = item.class_teacher?.user?.name || 'N/A';
            tr.appendChild(teacherTd);

            const studentsTd = document.createElement('td');
            const studentsBadge = document.createElement('span');
            studentsBadge.className = 'badge bg-light text-dark border';
            studentsBadge.textContent = item.students_count || 0;
            studentsTd.appendChild(studentsBadge);
            tr.appendChild(studentsTd);

            const subjectsTd = document.createElement('td');
            const subjectsBadge = document.createElement('span');
            subjectsBadge.className = 'badge bg-info-subtle text-info';
            subjectsBadge.textContent = item.subjects_count || 0;
            subjectsTd.appendChild(subjectsBadge);
            tr.appendChild(subjectsTd);

            const assignmentsTd = document.createElement('td');
            const assignmentsBadge = document.createElement('span');
            assignmentsBadge.className = 'badge bg-warning-subtle text-warning';
            assignmentsBadge.textContent = item.assignments_count || 0;
            assignmentsTd.appendChild(assignmentsBadge);
            tr.appendChild(assignmentsTd);
        } else if (type === 'school') {
            const nameTd = document.createElement('td');
            const schoolName = document.createElement('div');
            schoolName.className = 'fw-bold text-dark';
            schoolName.textContent = item.name;

            const schoolEmail = document.createElement('div');
            schoolEmail.className = 'small text-muted';
            schoolEmail.textContent = item.email || '';

            const schoolPhone = document.createElement('div');
            schoolPhone.className = 'small text-muted';
            schoolPhone.textContent = item.phone || '';

            nameTd.append(schoolName, schoolEmail, schoolPhone);
            tr.appendChild(nameTd);

            const locTd = document.createElement('td');
            const addressDiv = document.createElement('div');
            addressDiv.className = 'small text-wrap';
            addressDiv.style.maxWidth = '200px';
            addressDiv.textContent = item.address || '';

            const cityDiv = document.createElement('div');
            cityDiv.className = 'small text-muted';
            cityDiv.textContent = `${item.city || ''}, ${item.state || ''}`.replace(/^, /, '');

            locTd.append(addressDiv, cityDiv);
            tr.appendChild(locTd);

            const personTd = document.createElement('td');
            const personName = document.createElement('div');
            personName.textContent = item.contact_person || 'N/A';

            const personPhone = document.createElement('div');
            personPhone.className = 'small text-muted';
            personPhone.textContent = item.contact_person_phone || '';

            personTd.append(personName, personPhone);
            tr.appendChild(personTd);

            const statsTd = document.createElement('td');
            const usersBadge = document.createElement('span');
            usersBadge.className = 'badge bg-light text-dark border me-1';
            usersBadge.innerHTML = `<i class="bi bi-people"></i> `;
            usersBadge.append(document.createTextNode(item.users_count || 0));

            const studentsBadge = document.createElement('span');
            studentsBadge.className = 'badge bg-light text-dark border';
            studentsBadge.innerHTML = `<i class="bi bi-mortarboard"></i> `;
            studentsBadge.append(document.createTextNode(item.students_count || 0));

            statsTd.append(usersBadge, studentsBadge);
            tr.appendChild(statsTd);

            const planTd = document.createElement('td');
            const planBadge = document.createElement('span');
            planBadge.className = 'badge bg-info-subtle text-info text-uppercase';
            planBadge.textContent = item.plan || 'Basic';
            planTd.appendChild(planBadge);
            tr.appendChild(planTd);

            const statusTd = document.createElement('td');
            const isActive = item.is_active || item.status === 'active';
            const statusBadge = document.createElement('span');
            statusBadge.className = `badge bg-${isActive ? 'success' : 'danger'}-subtle text-${isActive ? 'success' : 'danger'} px-2`;
            statusBadge.textContent = item.status_label || (isActive ? 'Active' : 'Inactive');
            statusTd.appendChild(statusBadge);
            tr.appendChild(statusTd);
        } else if (type === 'teacher') {
            // ... (keep teacher logic same)
            const teacherTd = document.createElement('td');
            const teacherDiv = document.createElement('div');
            teacherDiv.className = 'd-flex align-items-center';

            const teacherAvatar = document.createElement('div');
            teacherAvatar.className = 'avatar-sm me-2 bg-light rounded text-center';
            teacherAvatar.style.cssText = 'width:32px; height:32px; line-height:32px;';
            teacherAvatar.innerHTML = '<i class="bi bi-person-workspace text-primary"></i>';

            const teacherInfo = document.createElement('div');
            const teacherName = document.createElement('div');
            teacherName.className = 'fw-bold text-dark';
            teacherName.textContent = item.name || item.full_name || item.user?.name || 'N/A';

            const teacherEmail = document.createElement('div');
            teacherEmail.className = 'small text-muted';
            teacherEmail.textContent = item.user?.email || item.email || '';

            teacherInfo.append(teacherName, teacherEmail);
            teacherDiv.append(teacherAvatar, teacherInfo);
            teacherTd.appendChild(teacherDiv);
            tr.appendChild(teacherTd);

            const employeeTd = document.createElement('td');
            const employeeBadge = document.createElement('span');
            employeeBadge.className = 'badge bg-light text-dark border';
            employeeBadge.textContent = item.employee_number || 'N/A';
            employeeTd.appendChild(employeeBadge);
            tr.appendChild(employeeTd);

            const statusTd = document.createElement('td');
            const isActive = (item.status === 'active' || item.status === 1 || item.user?.status === 'active');
            const statusBadge = document.createElement('span');
            statusBadge.className = `badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2`;
            statusBadge.textContent = isActive ? 'Active' : 'Inactive';
            statusTd.appendChild(statusBadge);
            tr.appendChild(statusTd);

            const subjectsTd = document.createElement('td');
            subjectsTd.textContent = item.assignments_count !== undefined ? `${item.assignments_count} Subjects` : 'N/A';
            tr.appendChild(subjectsTd);

            const phoneTd = document.createElement('td');
            phoneTd.textContent = item.user?.phone || item.phone || 'N/A';
            tr.appendChild(phoneTd);

            const dateTd = document.createElement('td');
            dateTd.textContent = item.hire_date ? new Date(item.hire_date).toLocaleDateString() : 'N/A';
            tr.appendChild(dateTd);
        } else {
            // Generic fallback
            const nameTd = document.createElement('td');
            nameTd.textContent = item.name || item.title || 'N/A';
            tr.appendChild(nameTd);
        }

        const actionTd = document.createElement('td');
        actionTd.className = 'text-end';

        // Actions
        if (userRole !== 'student') {
            if (type === 'class') {
                const manageBtn = document.createElement('button');
                manageBtn.className = 'btn btn-sm btn-outline-primary ms-1';
                manageBtn.title = 'Manage Subjects';
                const i = document.createElement('i');
                i.className = 'bi bi-journal-plus';
                manageBtn.appendChild(i);
                manageBtn.onclick = () => window.manageSubjects ? window.manageSubjects(item.id, item.name) : console.warn('manageSubjects missing');
                actionTd.appendChild(manageBtn);
            }

            if (type === 'school' && !item.is_active) {
                const approveBtn = document.createElement('button');
                approveBtn.type = 'button';
                approveBtn.className = 'btn btn-sm btn-success-subtle text-success me-1';
                const approveIcon = document.createElement('i');
                approveIcon.className = 'bi bi-check-lg';
                approveBtn.appendChild(approveIcon);
                approveBtn.title = 'Approve School';
                approveBtn.onclick = () => window.approveSchool ? window.approveSchool(item.id) : console.warn('approveSchool fn missing');
                actionTd.appendChild(approveBtn);
            }

            actionTd.append(
                actionButton('bi-pencil-square', 'edit', type, item.id),
                actionButton('bi-trash', 'delete', type, item.id)
            );
        }

        tr.appendChild(actionTd);
        return tr;
    };

    const actionButton = (iconClass, action, entity, id) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `btn btn-sm btn-outline-${action === 'delete' ? 'danger' : 'primary'} ms-1`;

        const i = document.createElement('i');
        i.className = `bi ${iconClass}`;
        btn.appendChild(i);

        btn.dataset.action = action;
        btn.dataset.entity = entity;
        btn.dataset.id = id;
        return btn;
    };

    const createSkeletonRow = (cols = 6) => {
        const tr = document.createElement('tr');
        tr.className = 'skeleton-row';
        for (let i = 0; i < cols; i++) {
            const td = document.createElement('td');
            const skeleton = document.createElement('div');
            skeleton.className = 'skeleton-line';
            td.appendChild(skeleton);
            tr.appendChild(td);
        }
        return tr;
    };

    const emptyRow = (message, isError = false) => {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 10;
        td.className = `text-center py-4 ${isError ? 'text-danger' : 'text-muted'}`;
        td.textContent = message;
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
            deleteItem(`/api/v1/${endpoint}/${id}`);
        }

        if (action === 'edit') {
            btn.disabled = true;
            btn.replaceChildren();
            const spinner = document.createElement('span');
            spinner.className = 'spinner-border spinner-border-sm';
            btn.appendChild(spinner);

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
            } catch (err) {
                console.error(`Failed to fetch ${entity} data:`, err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load item data.' });
            } finally {
                btn.disabled = false;
                btn.replaceChildren();
                const i = document.createElement('i');
                i.className = action === 'delete' ? 'bi bi-trash' : 'bi bi-pencil-square';
                btn.appendChild(i);
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
            submitBtn.replaceChildren();
            const spinner = document.createElement('span');
            spinner.className = 'spinner-border spinner-border-sm me-2';
            submitBtn.append(spinner, document.createTextNode('Saving...'));
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
        if (!errors || typeof errors !== 'object') return;

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

    const deleteItem = async (url) => {
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
                location.reload();
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

    const logout = async (e) => {
        if (e && e.preventDefault) e.preventDefault();

        try {
            await axios.post('/logout', {}, {
                headers: { 'Accept': 'application/json' }
            });
        } catch (err) {
            console.warn('Logout API failed, forcing local cleanup', err);
        } finally {
            // Clear all client-side storage
            localStorage.clear();
            sessionStorage.clear();

            // redirect - force reload from server
            window.location.replace('/');
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
            let data = res?.data?.data || res?.data || [];

            // Handle Laravel Pagination
            if (data && !Array.isArray(data) && Array.isArray(data.data)) {
                data = data.data;
            }

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = placeholder;
            select.appendChild(defaultOpt);

            data.forEach(item => {
                const opt = document.createElement('option');

                const val = typeof valueKey === 'function' ? valueKey(item) : item[valueKey];
                const lbl = typeof labelKey === 'function' ? labelKey(item) : item[labelKey];

                opt.value = val;
                opt.textContent = lbl;

                if (selectedId && String(val) === String(selectedId)) opt.selected = true;
                select.appendChild(opt);
            });

            select.disabled = false;
        } catch {
            select.replaceChildren();
            const opt = document.createElement('option');
            opt.textContent = 'Failed to load';
            select.appendChild(opt);
        }
    };

    const initiateLinking = async () => {
        const email = document.getElementById('link-email').value;
        if (!email) {
            Swal.fire({ icon: 'warning', title: 'Email required', text: 'Please enter a valid email address.' });
            return;
        }

        try {
            await axios.post('/api/v1/account/link/initiate', { email });
            document.getElementById('display-link-email').textContent = email;
            switchLinkStep(2);
            Swal.fire({ icon: 'success', title: 'Step 1 Complete', text: 'Verification code sent!' });
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Failed', text: err.response?.data?.message || 'Failed to send code' });
        }
    };

    const verifyLinking = async () => {
        const email = document.getElementById('link-email').value;
        const otp = document.getElementById('link-otp').value;

        if (!otp || otp.length !== 6) {
            Swal.fire({ icon: 'warning', title: 'Invalid code', text: 'Please enter the 6-digit verification code.' });
            return;
        }

        try {
            await axios.post('/api/v1/account/link/verify', { email, otp });
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Account linked successfully.' });
            closeModal('linkAccountModal');
            location.reload();
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Failed', text: err.response?.data?.message || 'Invalid or expired code' });
        }
    };

    const switchLinkStep = (step) => {
        const s1 = document.getElementById('link-step-1');
        const s2 = document.getElementById('link-step-2');
        if (step === 1) {
            s1.classList.remove('d-none');
            s2.classList.add('d-none');
        } else {
            s1.classList.add('d-none');
            s2.classList.remove('d-none');
        }
    };

    const resetForm = (form) => {
        if (!form) return;
        form.reset();
        clearFormErrors(form);
        // Clear hidden IDs that might be left over from previous edits
        form.querySelectorAll('input[type="hidden"][name$="_id"]').forEach(i => i.value = '');
    };

    const formatCurrency = (val) =>
        new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val || 0);

    const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);

    return { init, renderTable, submitForm, populateForm, resetForm, deleteItem, logout, loadOptions, safeHTML, formatCurrency, initiateLinking, verifyLinking, switchLinkStep };
})();

window.App = App;

document.addEventListener('DOMContentLoaded', async () => {
    const sidebar = new SidebarManager();
    await sidebar.init();
    App.init();
});
