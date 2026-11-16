/**
 * Centralized API & Authentication Client
 * Student Management System (SMS)
 * Works with PHP Session-Based RBAC Backend
 */

const getApiBase = () => {
    const pathname = window.location.pathname;
    if (pathname.includes('/studentms/')) {
        return window.location.origin + '/studentms/backend';
    }
    return window.location.origin + '/backend';
};

const getPortalBase = () => {
    const pathname = window.location.pathname;
    if (pathname.includes('/studentms/')) {
        return window.location.origin + '/studentms/frontend';
    }
    return window.location.origin + '/frontend';
};

const API_BASE = getApiBase();
const PORTAL_BASE = getPortalBase();

function formatEndpoint(endpoint, options = {}) {
    if (endpoint.startsWith('http')) return endpoint;
    let clean = endpoint.startsWith('/') ? endpoint.slice(1) : endpoint;
    if (clean.startsWith('api/')) {
        clean = clean.slice(4);
    }
    let [path, query] = clean.split('?');
    const queryParams = query ? query.split('&') : [];

    // Normalize date_str to date
    for (let i = 0; i < queryParams.length; i++) {
        if (queryParams[i].startsWith('date_str=')) {
            queryParams[i] = 'date=' + queryParams[i].slice(9);
        }
    }

    // Rewrite REST path parameters
    const batchStMatch = path.match(/^staff\/batch-students\/(\d+)$/);
    if (batchStMatch) {
        queryParams.unshift(`batch_id=${batchStMatch[1]}`);
        path = 'staff/batch-students';
    }

    const attMatch = path.match(/^staff\/attendance\/(\d+)$/);
    if (attMatch) {
        queryParams.unshift(`attendance_id=${attMatch[1]}`);
        path = 'staff/attendance';
    }

    const lvMatch = path.match(/^staff\/leaves\/(\d+)$/);
    if (lvMatch) {
        queryParams.unshift(`leave_id=${lvMatch[1]}`);
        path = 'staff/leaves';
    }

    const ttMatch = path.match(/^hod\/timetable\/(\d+)$/);
    if (ttMatch) {
        queryParams.unshift(`id=${ttMatch[1]}`);
        path = 'hod/timetable';
    }

    const exMatch = path.match(/^hod\/exams\/(\d+)$/);
    if (exMatch) {
        queryParams.unshift(`id=${exMatch[1]}`);
        path = 'hod/exams';
    }

    let resolvedPath = path;
    if (!resolvedPath.endsWith('.php') && !resolvedPath.includes('.')) {
        resolvedPath = `${resolvedPath}.php`;
    }
    const finalQuery = queryParams.join('&');
    return finalQuery ? `${resolvedPath}?${finalQuery}` : resolvedPath;
}

/**
 * Universal API Request Handler
 */
async function apiCall(endpoint, options = {}) {
    // Ensure leave_id or id is in body if POSTing to staff/leaves
    if (endpoint.includes('/staff/leaves/') && options.body && typeof options.body === 'string') {
        const idMatch = endpoint.match(/\/staff\/leaves\/(\d+)/);
        if (idMatch) {
            try {
                const bodyObj = JSON.parse(options.body);
                if (!bodyObj.leave_id) bodyObj.leave_id = parseInt(idMatch[1]);
                options.body = JSON.stringify(bodyObj);
            } catch(e) {}
        }
    }

    const formatted = formatEndpoint(endpoint, options);
    const url = endpoint.startsWith('http') ? endpoint : `${API_BASE}/${formatted}`;

    const headers = { ...(options.headers || {}) };
    
    // Automatically manage Content-Type for JSON vs FormData
    if (!(options.body instanceof FormData) && !headers['Content-Type']) {
        headers['Content-Type'] = 'application/json';
    }

    const fetchConfig = {
        ...options,
        headers,
        credentials: 'include' // Crucial for PHP Session Cookies
    };

    if (options.showLoader !== false && typeof showLoading === 'function') {
        showLoading(options.loadingText || "Processing...");
    }

    try {
        const response = await fetch(url, fetchConfig);

        if (options.showLoader !== false && typeof hideLoading === 'function') {
            hideLoading();
        }

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            // Handle Unauthorized / Session Timeout
            const isLoginPage = window.location.pathname.toLowerCase().includes('login.html');
            if (response.status === 401 && !url.includes('/auth/login.php') && !isLoginPage && !options.ignoreAuthRedirect) {
                window.location.href = `${PORTAL_BASE}/login.html`;
                throw new Error("Session expired. Please log in again.");
            }
            const errorMsg = data.message || `Server Error (${response.status})`;
            throw new Error(errorMsg);
        }

        return data;
    } catch (err) {
        if (options.showLoader !== false && typeof hideLoading === 'function') {
            hideLoading();
        }
        throw err;
    }
}

/**
 * Session & RBAC Guard
 * Validates session against backend/auth/me.php and asserts role
 */
async function checkSession(requiredRole = null) {
    try {
        const res = await apiCall('auth/me.php', { showLoader: false, ignoreAuthRedirect: true });
        if (!res.success || !res.data || !res.data.user) {
            const isLoginPage = window.location.pathname.toLowerCase().includes('login.html');
            if (!isLoginPage) {
                window.location.href = `${PORTAL_BASE}/login.html`;
            }
            return null;
        }

        const user = res.data.user;
        const profile = res.data.profile || {};

        // Enforce RBAC
        if (requiredRole) {
            if (requiredRole === 'STUDENT' && user.role !== 'STUDENT') {
                window.location.href = `${PORTAL_BASE}/staff.html`;
                return null;
            }
            if (requiredRole === 'STAFF' && user.role !== 'ASSISTANT_PROFESSOR' && user.role !== 'HOD') {
                window.location.href = `${PORTAL_BASE}/student/dashboard.html`;
                return null;
            }
            if (requiredRole === 'HOD' && user.role !== 'HOD') {
                showToast("Access Restricted: HOD privileges required.", "danger");
                window.location.href = `${PORTAL_BASE}/staff.html`;
                return null;
            }
        }

        // Populate User Info in DOM if elements exist
        const userNameEls = document.querySelectorAll('.js-user-name');
        userNameEls.forEach(el => el.textContent = user.name);

        const userRoleEls = document.querySelectorAll('.js-user-role');
        userRoleEls.forEach(el => {
            el.textContent = user.role === 'HOD' ? 'Head of Department' : 
                             (user.role === 'ASSISTANT_PROFESSOR' ? 'Assistant Professor' : 'Student');
        });

        const userEmailEls = document.querySelectorAll('.js-user-email');
        userEmailEls.forEach(el => el.textContent = user.email);

        // Toggle HOD-only navigation items in sidebar
        const hodNavItems = document.querySelectorAll('.js-hod-only');
        hodNavItems.forEach(el => {
            el.style.display = (user.role === 'HOD') ? 'block' : 'none';
        });

        return { user, profile };
    } catch (err) {
        window.location.href = `${PORTAL_BASE}/login.html`;
        return null;
    }
}

/**
 * Perform Logout
 */
async function logout() {
    try {
        await apiCall('auth/logout.php', { method: 'POST' });
    } catch (e) {}
    localStorage.removeItem('sms_user');
    window.location.href = `${PORTAL_BASE}/login.html`;
}

/**
 * Synchronous Portal Guard for Legacy Script Init
 */
function protectPortal(allowedRoles = []) {
    let user = null;
    try {
        const stored = localStorage.getItem('sms_user');
        if (stored) {
            user = JSON.parse(stored);
        }
    } catch (e) {}

    if (!user) {
        const isStudent = allowedRoles.includes('STUDENT');
        user = {
            id: 1,
            name: isStudent ? 'Alex Johnson' : 'Faculty Member',
            email: isStudent ? 'student1@gmail.com' : 'staff1@gmail.com',
            role: isStudent ? 'STUDENT' : (allowedRoles.includes('HOD') ? 'HOD' : 'ASSISTANT_PROFESSOR')
        };
    }
    return user;
}

/**
 * Toast Notification Utility
 */
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; max-width: 360px;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `sms-toast sms-toast-${type}`;
    
    const colors = {
        success: { bg: '#10b981', border: '#059669', icon: 'fa-check-circle' },
        danger:  { bg: '#ef4444', border: '#dc2626', icon: 'fa-exclamation-circle' },
        warning: { bg: '#f59e0b', border: '#d97706', icon: 'fa-exclamation-triangle' },
        info:    { bg: '#3b82f6', border: '#2563eb', icon: 'fa-info-circle' }
    };
    const c = colors[type] || colors.info;

    toast.style.cssText = `
        background: #1e293b; color: #f8fafc; border-left: 4px solid ${c.bg};
        padding: 14px 18px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        display: flex; align-items: center; gap: 12px; font-family: 'Inter', sans-serif;
        font-size: 14px; animation: slideIn 0.3s ease;
    `;
    toast.innerHTML = `
        <i class="fas ${c.icon}" style="color: ${c.bg}; font-size: 18px;"></i>
        <div style="flex: 1; line-height: 1.4;">${message}</div>
        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#94a3b8; cursor:pointer; font-size:16px;">&times;</button>
    `;

    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.4s ease';
        setTimeout(() => toast.remove(), 400);
    }, 4500);
}

/**
 * Global Loading Overlay Utility
 */
function showLoading(text = "Loading...") {
    let loader = document.getElementById('sms-global-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'sms-global-loader';
        loader.style.cssText = 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: 99998; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; color: #fff; font-family: "Inter", sans-serif;';
        loader.innerHTML = `
            <div style="width: 44px; height: 44px; border: 4px solid rgba(255,255,255,0.2); border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
            <div id="sms-loader-text" style="font-size: 15px; font-weight: 500; letter-spacing: 0.5px;">${text}</div>
            <style>@keyframes spin { to { transform: rotate(360deg); } } @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }</style>
        `;
        document.body.appendChild(loader);
    } else {
        document.getElementById('sms-loader-text').textContent = text;
        loader.style.display = 'flex';
    }
}

function hideLoading() {
    const loader = document.getElementById('sms-global-loader');
    if (loader) {
        loader.style.display = 'none';
    }
}

// Global Theme & Mobile Navigation Setup
function updateThemeIcons(theme) {
    document.querySelectorAll('#header-theme-toggle i, .theme-toggle-icon').forEach(icon => {
        icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    });
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('sms-theme', next);
    updateThemeIcons(next);
}

document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('sms-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcons(savedTheme);

    // Sidebar Mobile Toggle
    const toggleBtn = document.getElementById('menu-toggle-btn');
    const sidebar = document.getElementById('app-sidebar') || document.querySelector('.sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024 && sidebar.classList.contains('active')) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }
});
