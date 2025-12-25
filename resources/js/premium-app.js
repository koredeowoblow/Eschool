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
        } catch (err) {
            console.error('Dashboard load error:', err);
            root.textContent = 'Failed to load dashboard data.';
        }
    };

    const renderPlatformStats = (root, stats) => {
        const cards = [
            ['Total Schools', stats.total_schools],
            ['Total Users', stats.total_users],
            ['Total Revenue', formatCurrency(stats.total_revenue)]
        ];

        cards.forEach(([label, value]) => {
            root.appendChild(createStatCard(label, value));
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
        root.appendChild(createStatCard('Paid Invoices', stats.finance?.invoices?.paid || 0, 'bi-check-all'));
        root.appendChild(
            createStatCard(
                'Overdue Invoices',
                stats.finance?.invoices?.overdue || 0,
                'bi-exclamation-triangle-fill'
            )
        );

        // Row 3: Financial Health
        root.appendChild(
            createStatCard(
                'Total Revenue',
                formatCurrency(stats.finance?.payments?.total_amount || 0),
                'bi-cash-stack'
            )
        );
        root.appendChild(
            createStatCard(
                'Month Revenue',
                formatCurrency(stats.finance?.payments?.this_month_amount || 0),
                'bi-wallet2'
            )
        );
        root.appendChild(
            createStatCard(
                'Outstanding Balance',
                formatCurrency(stats.finance?.outstanding_balance || 0),
                'bi-piggy-bank'
            )
        );

        const activityRoot = document.getElementById('dashboard-activity-root');
        if (activityRoot && Array.isArray(stats.academic?.upcoming_assignments)) {
            renderActivity(activityRoot, stats.academic.upcoming_assignments);
        }
    };

    const createStatCard = (label, value, icon = 'bi-graph-up') => {
        const col = document.createElement('div');
        col.className = 'col-md-4';

        const card = document.createElement('div');
        card.className = 'card-premium h-100 p-3 d-flex align-items-center gap-3';

        const iconBox = document.createElement('div');
        iconBox.className = 'avatar-md bg-primary-subtle rounded-circle text-primary d-flex align-items-center justify-content-center';
        iconBox.style.width = '48px';
        iconBox.style.height = '48px';
        iconBox.innerHTML = `<i class="bi ${icon} fs-4"></i>`;

        const content = document.createElement('div');

        const p = document.createElement('p');
        p.className = 'text-muted text-uppercase small mb-1';
        p.textContent = label;

        const h = document.createElement('h3');
        h.className = 'h3 mb-0';
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

    const renderTable = async (url, tbodyId, entityType) => {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                    <span class="ms-2 text-muted">Loading data...</span>
                </td>
            </tr>
        `;

        try {
            const res = await axios.get(url);
            const items = res?.data?.data;

            tbody.replaceChildren();

            if (!Array.isArray(items) || items.length === 0) {
                tbody.appendChild(emptyRow('No records found'));
                return;
            }

            items.forEach(item => {
                tbody.appendChild(renderTableRow(item, entityType));
            });
        } catch (err) {
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
            statusTd.innerHTML = `<span class="badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2">${item.status}</span>`;
            tr.appendChild(statusTd);

            const actionTd = document.createElement('td');
            actionTd.className = 'text-end';

            if (userRole === 'student') {
                // Student Action: Submit
                // Check if already submitted? (Ideally backend sends 'submission_status')
                // For now, just show "Submit"
                const submitBtn = document.createElement('button');
                submitBtn.className = 'btn btn-sm btn-primary-premium ms-1';
                submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit';
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
            tr.appendChild(studentTd);

            const admissionTd = document.createElement('td');
            admissionTd.innerHTML = `<span class="badge bg-light text-dark border">${escapeText(item.admission_number)}</span>`;
            tr.appendChild(admissionTd);

            const gradeTd = document.createElement('td');
            gradeTd.textContent = item.current_grade || item.grade?.name || 'N/A';
            tr.appendChild(gradeTd);

            const genderTd = document.createElement('td');
            genderTd.innerHTML = `<span class="text-capitalize">${escapeText(item.gender || item.user?.gender || 'N/A')}</span>`;
            tr.appendChild(genderTd);

            const statusTd = document.createElement('td');
            const isActive = item.status === true || item.status === 1 || item.status === 'active';
            statusTd.innerHTML = `<span class="badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2">
                ${isActive ? 'Active' : 'Inactive'}
            </span>`;
            tr.appendChild(statusTd);
        } else if (type === 'school') {
            const nameTd = document.createElement('td');
            nameTd.innerHTML = `
                <div class="fw-bold text-dark">${item.name}</div>
                <div class="small text-muted">${item.email || ''}</div>
                <div class="small text-muted">${item.phone || ''}</div>
            `;
            tr.appendChild(nameTd);

            const locTd = document.createElement('td');
            locTd.innerHTML = `
                <div class="small text-wrap" style="max-width: 200px;">${item.address || ''}</div>
                <div class="small text-muted">${item.city || ''}, ${item.state || ''}</div>
            `;
            tr.appendChild(locTd);

            const personTd = document.createElement('td');
            personTd.innerHTML = `
                <div>${item.contact_person || 'N/A'}</div>
                <div class="small text-muted">${item.contact_person_phone || ''}</div>
            `;
            tr.appendChild(personTd);

            const statsTd = document.createElement('td');
            statsTd.innerHTML = `
                <span class="badge bg-light text-dark border me-1"><i class="bi bi-people"></i> ${item.users_count || 0}</span>
                <span class="badge bg-light text-dark border"><i class="bi bi-mortarboard"></i> ${item.students_count || 0}</span>
            `;
            tr.appendChild(statsTd);

            const planTd = document.createElement('td');
            planTd.innerHTML = `<span class="badge bg-info-subtle text-info text-uppercase">${item.plan || 'Basic'}</span>`;
            tr.appendChild(planTd);

            const statusTd = document.createElement('td');
            const isActive = item.is_active || item.status === 'active';
            statusTd.innerHTML = `<span class="badge bg-${isActive ? 'success' : 'danger'}-subtle text-${isActive ? 'success' : 'danger'} px-2">
                ${item.status_label || (isActive ? 'Active' : 'Inactive')}
            </span>`;
            tr.appendChild(statusTd);

        } else if (type === 'teacher') {
            // ... (keep teacher logic same)
            const teacherTd = document.createElement('td');
            teacherTd.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-2 bg-light rounded text-center" style="width:32px; height:32px; line-height:32px;">
                        <i class="bi bi-person-workspace text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">${escapeText(item.name || item.full_name || item.user?.name)}</div>
                        <div class="small text-muted">${escapeText(item.user?.email || item.email || '')}</div>
                    </div>
                </div>
            `;
            tr.appendChild(teacherTd);

            const employeeTd = document.createElement('td');
            employeeTd.innerHTML = `<span class="badge bg-light text-dark border">${escapeText(item.employee_number || 'N/A')}</span>`;
            tr.appendChild(employeeTd);

            const statusTd = document.createElement('td');
            const isActive = item.status === true || item.status === 1 || item.status === 'active';
            statusTd.innerHTML = `<span class="badge bg-${isActive ? 'success' : 'secondary'}-subtle text-${isActive ? 'success' : 'secondary'} px-2">
                ${isActive ? 'Active' : 'Inactive'}
            </span>`;
            tr.appendChild(statusTd);
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
            if (type === 'school' && !item.is_active) {
                const approveBtn = document.createElement('button');
                approveBtn.type = 'button';
                approveBtn.className = 'btn btn-sm btn-success-subtle text-success me-1';
                approveBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
                approveBtn.title = 'Approve School';
                approveBtn.onclick = () => window.approveSchool ? window.approveSchool(item.id) : console.warn('approveSchool fn missing');
                actionTd.appendChild(approveBtn);
            }

            actionTd.append(
                actionButton('<i class="bi bi-pencil-square"></i>', 'edit', type, item.id),
                actionButton('<i class="bi bi-trash"></i>', 'delete', type, item.id)
            );
        }

        tr.appendChild(actionTd);
        return tr;
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

    const logout = async () => {
        try {
            await axios.post('/logout');
        } catch (e) {
            console.warn('Logout API failed, forcing local cleanup', e);
        } finally {
            // Clear all client-side storage
            localStorage.clear();
            sessionStorage.clear();

            // redirect
            location.href = '/login';
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

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = placeholder;
            select.appendChild(defaultOpt);

            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item[valueKey];
                opt.textContent = item[labelKey];
                if (selectedId && String(item[valueKey]) === String(selectedId)) opt.selected = true;
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

    const formatCurrency = (val) =>
        new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val || 0);

    const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);

    return { init, renderTable, submitForm, populateForm, deleteItem, logout, loadOptions };
})();

window.App = App;

document.addEventListener('DOMContentLoaded', async () => {
    const sidebar = new SidebarManager();
    await sidebar.init();
    App.init();
});
