/* ==========================================================================
   STUDENT MANAGEMENT SYSTEM - STUDENT PORTAL LOGIC (js/student.js)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Session Verification
    let sessionUserStr = localStorage.getItem('sms_user') || localStorage.getItem('sms_v2_user');
    if (!sessionUserStr) {
        // Create default student user session if none found
        const defaultStudent = { id: 2, name: 'Alex Johnson', email: 'student@gmail.com', role: 'student', department: 'ECE', roll_no: 'ECE2026-042', semester: '4th Semester' };
        localStorage.setItem('sms_user', JSON.stringify(defaultStudent));
        sessionUserStr = JSON.stringify(defaultStudent);
    }

    const currentUser = JSON.parse(sessionUserStr);

    // 2. Global State & DOM Setup
    const userDept = currentUser.department || 'ECE';

    const avatarEl = document.getElementById('header-user-avatar');
    const nameEl = document.getElementById('header-user-name');
    const roleEl = document.getElementById('header-user-role');
    const greetingEl = document.getElementById('dash-greeting-name');

    if (avatarEl) avatarEl.textContent = currentUser.name.charAt(0).toUpperCase();
    if (nameEl) nameEl.textContent = currentUser.name;
    if (roleEl) roleEl.textContent = `Student (${userDept})`;
    if (greetingEl) greetingEl.textContent = currentUser.name;

    // Theme Toggle
    const themeBtn = document.getElementById('header-theme-toggle');
    const savedTheme = localStorage.getItem('sms-theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    if (themeBtn) {
        themeBtn.querySelector('i').className = savedTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        themeBtn.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('sms-theme', next);
            themeBtn.querySelector('i').className = next === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        });
    }

    // Mobile Sidebar Toggle
    const menuBtn = document.getElementById('menu-toggle-btn');
    const sidebar = document.getElementById('app-sidebar');
    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('active') && !sidebar.contains(e.target) && e.target !== menuBtn) {
                sidebar.classList.remove('active');
            }
        });
    }

    // Sign Out Action
    const signOutBtn = document.getElementById('nav-sign-out');
    if (signOutBtn) {
        signOutBtn.addEventListener('click', () => {
            localStorage.removeItem('sms_user');
            localStorage.removeItem('sms_v2_user');
            window.location.href = 'login.html';
        });
    }

    // Global Toast Notification Helper
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        let iconClass = 'fa-circle-check';
        if (type === 'error') iconClass = 'fa-circle-xmark';
        if (type === 'warning') iconClass = 'fa-triangle-exclamation';
        if (type === 'info') iconClass = 'fa-circle-info';
        toast.innerHTML = `<i class="fa-solid ${iconClass}"></i><span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    };

    // 3. Navigation View Switching
    window.navigateTo = function(viewName) {
        const views = {
            'dashboard': { el: document.getElementById('view-dashboard'), title: 'Dashboard Overview', sub: 'Welcome to your personal student portal' },
            'profile': { el: document.getElementById('view-profile'), title: 'My Profile', sub: 'Personal academic credentials and contact info' },
            'attendance': { el: document.getElementById('view-attendance'), title: 'My Attendance', sub: 'Subject-wise attendance breakdown and eligibility' },
            'timetable': { el: document.getElementById('view-timetable'), title: 'My Timetable', sub: 'Weekly class schedule and lecture halls' },
            'apply-leave': { el: document.getElementById('view-apply-leave'), title: 'Apply Leave', sub: 'Submit digital leave request to department staff' },
            'leave-history': { el: document.getElementById('view-leave-history'), title: 'Leave Requests', sub: 'Track status of your submitted leave applications' }
        };

        Object.keys(views).forEach(k => {
            if (views[k].el) views[k].el.style.display = (k === viewName) ? 'block' : 'none';
        });

        document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-view') === viewName) {
                item.classList.add('active');
            }
        });

        const v = views[viewName] || views['dashboard'];
        const titleEl = document.getElementById('view-title');
        const subEl = document.getElementById('view-path-subtitle');
        const breadEl = document.getElementById('breadcrumb-current');

        if (titleEl) titleEl.textContent = v.title;
        if (subEl) subEl.textContent = v.sub;
        if (breadEl) breadEl.textContent = v.title;

        if (viewName === 'profile') loadStudentProfile();
        if (viewName === 'timetable') loadStudentTimetable();
        if (viewName === 'leave-history') loadStudentLeaveHistory();
    };

    document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
        item.addEventListener('click', () => {
            const target = item.getAttribute('data-view');
            if (target) navigateTo(target);
        });
    });

    // 4. Load Profile
    function loadStudentProfile() {
        const nameEl = document.getElementById('prof-name');
        const rollEl = document.getElementById('prof-roll');
        const deptEl = document.getElementById('prof-dept');
        const emailEl = document.getElementById('prof-email');

        if (nameEl) nameEl.textContent = currentUser.name;
        if (rollEl) rollEl.textContent = currentUser.roll_no || 'ECE2026-042';
        if (deptEl) deptEl.textContent = currentUser.department || 'ECE';
        if (emailEl) emailEl.textContent = currentUser.email;
    }

    // 5. Load Timetable
    async function loadStudentTimetable() {
        const tbody = document.getElementById('student-time-tbody');
        if (!tbody) return;
        try {
            const res = await apiFetch(`/timetable?dept=${userDept}`);
            const slots = (res && res.data && res.data.timetable) ? res.data.timetable : [];

            tbody.innerHTML = slots.map(t => `
                <tr>
                    <td><strong>${t.day}</strong></td>
                    <td>${t.time}</td>
                    <td>${t.subject}</td>
                    <td><span class="badge badge-purple">${t.room}</span></td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--danger);">Failed to load timetable.</td></tr>`;
        }
    }

    // 6. Submit Leave Application
    const leaveForm = document.getElementById('student-leave-form');
    if (leaveForm) {
        leaveForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const leaveType = document.getElementById('leave-type').value;
            const startDate = document.getElementById('leave-start').value;
            const endDate = document.getElementById('leave-end').value;
            const reason = document.getElementById('leave-reason').value.trim();

            if (!startDate || !endDate || !reason) {
                showToast("Please fill in all leave request fields.", "warning");
                return;
            }

            try {
                await apiFetch('/leave', {
                    method: 'POST',
                    body: JSON.stringify({
                        student_id: currentUser.id || 2,
                        student_name: currentUser.name,
                        roll_no: currentUser.roll_no || 'ECE2026-042',
                        department: userDept,
                        leave_type: leaveType,
                        start_date: startDate,
                        end_date: endDate,
                        reason: reason
                    })
                });

                showToast("Leave request submitted successfully!");
                leaveForm.reset();
                navigateTo('leave-history');
            } catch (err) {
                showToast(err.message || "Failed to submit leave request.", "error");
            }
        });
    }

    // 7. Load Leave History
    async function loadStudentLeaveHistory() {
        const tbody = document.getElementById('student-leave-hist-tbody');
        if (!tbody) return;
        try {
            const res = await apiFetch(`/leave?dept=${userDept}`);
            const leaves = (res && res.data && res.data.leave_requests) ? res.data.leave_requests : [];

            if (leaves.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: var(--text-secondary);">No submitted leave applications found.</td></tr>`;
                return;
            }

            tbody.innerHTML = leaves.map(l => `
                <tr>
                    <td><span class="badge badge-info">${l.leave_type}</span></td>
                    <td>${l.start_date}</td>
                    <td>${l.end_date}</td>
                    <td>${l.reason}</td>
                    <td><span class="badge ${l.status.toLowerCase() === 'approved' ? 'badge-success' : (l.status.toLowerCase() === 'rejected' ? 'badge-danger' : 'badge-warning')}">${l.status}</span></td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: var(--danger);">Failed to load leave history.</td></tr>`;
        }
    }

    // Initial Load
    navigateTo('dashboard');
});
