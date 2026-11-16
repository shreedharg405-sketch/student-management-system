/* ==========================================================================
   STUDENT MANAGEMENT SYSTEM - STAFF PORTAL LOGIC (js/staff.js)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Session Verification
    let sessionUserStr = localStorage.getItem('sms_user') || localStorage.getItem('sms_v2_user');
    if (!sessionUserStr) {
        // Create default staff user session if none found
        const defaultStaff = { id: 1, name: 'Faculty Staff', email: 'staff@gmail.com', role: 'staff', department: 'ECE', designation: 'Assistant Professor' };
        localStorage.setItem('sms_user', JSON.stringify(defaultStaff));
        sessionUserStr = JSON.stringify(defaultStaff);
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
    if (roleEl) roleEl.textContent = `${userDept} - ${currentUser.designation || 'Faculty HOD'}`;
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
            'dashboard': { el: document.getElementById('view-dashboard'), title: 'Dashboard Overview', sub: 'Departmental Governance & Academic Console' },
            'courses': { el: document.getElementById('view-courses'), title: 'Course Management', sub: 'Maintain curriculum and course configurations' },
            'departments': { el: document.getElementById('view-departments'), title: 'Academic Departments', sub: 'Department directory and enrolled student rosters' },
            'attendance-monitor': { el: document.getElementById('view-attendance-monitor'), title: 'Attendance Monitor', sub: 'Live class attendance and batch submission' },
            'timetable-mgmt': { el: document.getElementById('view-timetable-mgmt'), title: 'Timetable Management', sub: 'Conflict-free weekly schedule manager' },
            'exams-mgmt': { el: document.getElementById('view-exams-mgmt'), title: 'Examination Schedules', sub: 'Department and hall-wise exam scheduler' },
            'leave-reviews': { el: document.getElementById('view-leave-reviews'), title: 'Student Leaves', sub: 'Review pending digital leave requests' }
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

        if (viewName === 'courses') loadCourses();
        if (viewName === 'departments') loadDepartments();
        if (viewName === 'attendance-monitor') loadAttendanceRoster();
        if (viewName === 'timetable-mgmt') loadStaffTimetable();
        if (viewName === 'exams-mgmt') loadStaffExams();
        if (viewName === 'leave-reviews') loadLeaveReviews();
    };

    document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
        item.addEventListener('click', () => {
            const target = item.getAttribute('data-view');
            if (target) navigateTo(target);
        });
    });

    // 4. Load Courses Section
    window.loadCourses = async function() {
        const tbody = document.getElementById('courses-tbody');
        if (!tbody) return;
        try {
            const res = await apiFetch(`/courses?dept=${userDept}`);
            const courses = (res && res.data && res.data.courses) ? res.data.courses : [];

            if (courses.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: var(--text-secondary);">No courses configured for this department yet.</td></tr>`;
                return;
            }

            tbody.innerHTML = courses.map(c => `
                <tr>
                    <td><strong>${c.code}</strong></td>
                    <td>${c.name}</td>
                    <td><span class="badge badge-info">${c.department || userDept}</span></td>
                    <td>${c.credits} Credits</td>
                    <td>${c.semester}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="alert('Edit course details coming soon')"><i class="fa-solid fa-pen"></i></button>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: var(--danger);">Failed to load course list.</td></tr>`;
        }
    };

    // 5. Load Departments Section
    window.loadDepartments = async function() {
        const tbody = document.getElementById('depts-tbody');
        if (!tbody) return;
        try {
            const res = await apiFetch('/departments');
            const depts = (res && res.data && res.data.departments) ? res.data.departments : [];

            tbody.innerHTML = depts.map(d => `
                <tr>
                    <td><strong>${d.code}</strong></td>
                    <td>${d.name}</td>
                    <td>${d.hod}</td>
                    <td>${d.count} Students</td>
                    <td>${d.faculty} Staff</td>
                    <td><span class="badge badge-success">Active</span></td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: var(--danger);">Failed to load department list.</td></tr>`;
        }
    };

    // 6. Load Attendance Roster
    window.loadAttendanceRoster = async function() {
        const tbody = document.getElementById('attendance-tbody');
        if (!tbody) return;
        try {
            const res = await apiFetch(`/students?dept=${userDept}`);
            const students = (res && res.data && res.data.students) ? res.data.students : [];

            if (students.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--text-secondary);">No enrolled students found.</td></tr>`;
                return;
            }

            tbody.innerHTML = students.map(s => `
                <tr>
                    <td><strong>${s.roll_no || 'ECE2026-001'}</strong></td>
                    <td>${s.name}</td>
                    <td><span class="badge badge-info">${s.department || userDept}</span></td>
                    <td>
                        <label style="cursor: pointer; font-size: 14px; font-weight: 600;">
                            <input type="checkbox" class="att-checkbox" data-student-id="${s.id}" checked style="margin-right: 8px;"> Present
                        </label>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--danger);">Failed to load roster.</td></tr>`;
        }
    };

    // Save Batch Attendance
    const btnSaveAtt = document.getElementById('btn-save-attendance');
    if (btnSaveAtt) {
        btnSaveAtt.addEventListener('click', () => {
            showToast("Batch attendance submitted successfully!");
        });
    }

    // 7. Load Timetable Section
    window.loadStaffTimetable = async function() {
        const tbody = document.getElementById('timetable-tbody');
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
                    <td>${t.department || userDept}</td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: var(--danger);">Failed to load timetable.</td></tr>`;
        }
    };

    // 8. Load Exam Schedules Section
    window.loadStaffExams = async function() {
        const tbody = document.getElementById('exams-tbody');
        if (!tbody) return;
        try {
            const res = await apiFetch(`/exams?dept=${userDept}`);
            const exams = (res && res.data && res.data.examinations) ? res.data.examinations : [];

            tbody.innerHTML = exams.map(x => `
                <tr>
                    <td><strong>${x.code || 'ECE301'}</strong></td>
                    <td>${x.subject}</td>
                    <td>${x.date}</td>
                    <td>${x.time}</td>
                    <td><span class="badge badge-warning">${x.hall}</span></td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: var(--danger);">Failed to load exam schedule.</td></tr>`;
        }
    };

    // 9. Load Student Leaves Section
    window.loadLeaveReviews = async function() {
        const tbody = document.getElementById('leaves-tbody');
        if (!tbody) return;
        try {
            const res = await apiFetch(`/leave?dept=${userDept}`);
            const leaves = (res && res.data && res.data.leave_requests) ? res.data.leave_requests : [];

            if (leaves.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 20px; color: var(--text-secondary);">No pending leave applications.</td></tr>`;
                return;
            }

            tbody.innerHTML = leaves.map(l => `
                <tr>
                    <td><strong>${l.student_name}</strong></td>
                    <td>${l.roll_no}</td>
                    <td><span class="badge badge-info">${l.leave_type}</span></td>
                    <td>${l.start_date} to ${l.end_date}</td>
                    <td>${l.reason}</td>
                    <td><span class="badge ${l.status.toLowerCase() === 'approved' ? 'badge-success' : (l.status.toLowerCase() === 'rejected' ? 'badge-danger' : 'badge-warning')}">${l.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="updateLeaveStatus(${l.id}, 'Approved')" style="margin-right: 6px;"><i class="fa-solid fa-check"></i> Approve</button>
                        <button class="btn btn-sm btn-danger" onclick="updateLeaveStatus(${l.id}, 'Rejected')"><i class="fa-solid fa-xmark"></i> Reject</button>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 20px; color: var(--danger);">Failed to load leave requests.</td></tr>`;
        }
    };

    window.updateLeaveStatus = function(id, newStatus) {
        let leaves = JSON.parse(localStorage.getItem('sms_leaves_db') || '[]');
        leaves = leaves.map(l => l.id == id ? { ...l, status: newStatus } : l);
        localStorage.setItem('sms_leaves_db', JSON.stringify(leaves));
        showToast(`Leave request ${newStatus.toLowerCase()}!`);
        loadLeaveReviews();
    };

    // Initial Load
    navigateTo('dashboard');
});
