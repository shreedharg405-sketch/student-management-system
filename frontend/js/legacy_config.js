/* ==========================================================================
   STUDENT MANAGEMENT SYSTEM - CENTRAL API CONFIG & DATA BACKEND
   ========================================================================== */

const API_URL = "http://127.0.0.1:8001/api";

// Initialize Local Mock DB if not present
(function initLocalStorageDB() {
    if (!localStorage.getItem('sms_users_db')) {
        const initialUsers = [
            { id: 1, name: 'Faculty Staff', email: 'staff@gmail.com', password: 'password123', role: 'staff', department: 'ECE', designation: 'Assistant Professor' },
            { id: 2, name: 'Alex Johnson', email: 'student@gmail.com', password: 'password123', role: 'student', department: 'ECE', roll_no: 'ECE2026-042', semester: '4th Semester' }
        ];
        localStorage.setItem('sms_users_db', JSON.stringify(initialUsers));
    }

    if (!localStorage.getItem('sms_courses_db')) {
        const initialCourses = [
            { id: 1, code: 'ECE301', name: 'Digital Signal Processing', department: 'ECE', credits: 4, semester: '5th' },
            { id: 2, code: 'ECE302', name: 'Microprocessors & Microcontrollers', department: 'ECE', credits: 3, semester: '5th' },
            { id: 3, code: 'CSE101', name: 'Object Oriented Programming', department: 'CSE', credits: 4, semester: '2nd' },
            { id: 4, code: 'IT204', name: 'Database Management Systems', department: 'IT', credits: 3, semester: '4th' }
        ];
        localStorage.setItem('sms_courses_db', JSON.stringify(initialCourses));
    }

    if (!localStorage.getItem('sms_depts_db')) {
        const initialDepts = [
            { id: 1, code: 'ECE', name: 'Electronics & Communication Engineering', hod: 'Dr. R. Sharma', count: 120, faculty: 12 },
            { id: 2, code: 'CSE', name: 'Computer Science & Engineering', hod: 'Dr. A. Verma', count: 180, faculty: 18 },
            { id: 3, code: 'IT', name: 'Information Technology', hod: 'Dr. S. Patel', count: 90, faculty: 10 },
            { id: 4, code: 'MECH', name: 'Mechanical Engineering', hod: 'Dr. K. Singh', count: 75, faculty: 8 }
        ];
        localStorage.setItem('sms_depts_db', JSON.stringify(initialDepts));
    }

    if (!localStorage.getItem('sms_leaves_db')) {
        const initialLeaves = [
            { id: 1, student_id: 2, student_name: 'Alex Johnson', roll_no: 'ECE2026-042', department: 'ECE', reason: 'Medical Emergency', leave_type: 'Medical', start_date: '2026-09-22', end_date: '2026-09-24', status: 'Pending' }
        ];
        localStorage.setItem('sms_leaves_db', JSON.stringify(initialLeaves));
    }

    if (!localStorage.getItem('sms_timetable_db')) {
        const initialTimetable = [
            { id: 1, day: 'Monday', time: '09:00 AM - 10:00 AM', subject: 'Digital Signal Processing', room: 'Lab 201', department: 'ECE' },
            { id: 2, day: 'Monday', time: '10:15 AM - 11:15 AM', subject: 'Microprocessors', room: 'Room 304', department: 'ECE' },
            { id: 3, day: 'Tuesday', time: '11:30 AM - 12:30 PM', subject: 'VLSI Design', room: 'Lab 105', department: 'ECE' },
            { id: 4, day: 'Wednesday', time: '02:00 PM - 03:00 PM', subject: 'Communication Theory', room: 'Room 302', department: 'ECE' }
        ];
        localStorage.setItem('sms_timetable_db', JSON.stringify(initialTimetable));
    }

    if (!localStorage.getItem('sms_exams_db')) {
        const initialExams = [
            { id: 1, code: 'ECE301', subject: 'Digital Signal Processing', date: '2026-10-15', time: '10:00 AM - 01:00 PM', hall: 'Hall A1', department: 'ECE' },
            { id: 2, code: 'ECE302', subject: 'Microprocessors', date: '2026-10-17', time: '10:00 AM - 01:00 PM', hall: 'Hall B2', department: 'ECE' }
        ];
        localStorage.setItem('sms_exams_db', JSON.stringify(initialExams));
    }
})();

/**
 * Robust API Client with PHP & Local Storage fallback
 */
async function apiFetch(endpoint, options = {}) {
    const isPost = options.method === 'POST';
    const bodyData = options.body ? (typeof options.body === 'string' ? JSON.parse(options.body) : options.body) : {};

    // Determine target relative endpoint for PHP
    let phpEndpoint = '';
    if (endpoint.includes('/login')) phpEndpoint = 'php/login.php';
    else if (endpoint.includes('/signup')) phpEndpoint = 'php/signup.php';
    else if (endpoint.includes('/students')) phpEndpoint = 'php/students.php';
    else if (endpoint.includes('/courses')) phpEndpoint = 'php/courses.php';
    else if (endpoint.includes('/departments')) phpEndpoint = 'php/departments.php';
    else if (endpoint.includes('/attendance')) phpEndpoint = 'php/attendance.php';
    else if (endpoint.includes('/leave')) phpEndpoint = 'php/leave.php';
    else if (endpoint.includes('/timetable')) phpEndpoint = 'php/timetable.php';
    else if (endpoint.includes('/exams') || endpoint.includes('/examinations')) phpEndpoint = 'php/examinations.php';

    // 1. Try PHP endpoint if available under current path
    if (phpEndpoint) {
        try {
            const phpUrl = window.location.pathname.includes('/html/') ? `../${phpEndpoint}` : phpEndpoint;

            const res = await fetch(phpUrl, {
                method: options.method || 'GET',
                headers: { 'Content-Type': 'application/json' },
                body: options.body
            });
            if (res.ok) {
                const json = await res.json();
                if (json.status === 'success' || json.status === 'ok' || Array.isArray(json) || json.success) {
                    return json.status ? json : { status: 'success', data: json };
                }
            }
        } catch (e) {
            // Silence PHP error and proceed to local mock DB
        }
    }

    // 2. Try FastAPI Backend if running
    try {
        const url = endpoint.startsWith('http') ? endpoint : `${API_URL}${endpoint.startsWith('/') ? '' : '/'}${endpoint}`;
        const res = await fetch(url, {
            ...options,
            headers: { 'Content-Type': 'application/json', ...(options.headers || {}) }
        });
        if (res.ok) {
            const data = await res.json();
            return data;
        }
    } catch (e) {
        // Silence FastAPI error
    }

    // 3. Fallback to Local Storage Mock DB
    return handleLocalMockApi(endpoint, options, bodyData);
}

function handleLocalMockApi(endpoint, options, bodyData) {
    const isPost = (options.method || 'GET').toUpperCase() === 'POST';

    // Login Endpoint
    if (endpoint.includes('/login')) {
        const users = JSON.parse(localStorage.getItem('sms_users_db') || '[]');
        const emailOrId = (bodyData.email || bodyData.username || '').trim().toLowerCase();
        const pwd = bodyData.password || '';

        const user = users.find(u => 
            (u.email.toLowerCase() === emailOrId || (u.roll_no && u.roll_no.toLowerCase() === emailOrId)) &&
            u.password === pwd
        );

        if (user) {
            return Promise.resolve({
                status: 'success',
                message: 'Login successful',
                data: { user: user, token: 'mock-jwt-token' }
            });
        } else {
            return Promise.reject(new Error("Invalid email or password."));
        }
    }

    // Signup Endpoint
    if (endpoint.includes('/signup')) {
        const users = JSON.parse(localStorage.getItem('sms_users_db') || '[]');
        const email = (bodyData.email || '').trim().toLowerCase();

        if (users.some(u => u.email.toLowerCase() === email)) {
            return Promise.reject(new Error("An account with this email already exists."));
        }

        const newUser = {
            id: Date.now(),
            name: bodyData.name || 'User',
            email: bodyData.email,
            password: bodyData.password,
            role: bodyData.role || 'student',
            department: bodyData.department || 'ECE',
            roll_no: bodyData.role === 'student' ? `${bodyData.department || 'ECE'}2026-${Math.floor(100 + Math.random() * 900)}` : null,
            semester: '1st Semester'
        };

        users.push(newUser);
        localStorage.setItem('sms_users_db', JSON.stringify(users));

        return Promise.resolve({
            status: 'success',
            message: 'Registration successful! Please log in.',
            data: { user: newUser }
        });
    }

    // Students Endpoint
    if (endpoint.includes('/students')) {
        const users = JSON.parse(localStorage.getItem('sms_users_db') || '[]');
        const students = users.filter(u => u.role === 'student');
        return Promise.resolve({ status: 'success', data: { students: students } });
    }

    // Courses Endpoint
    if (endpoint.includes('/courses')) {
        let courses = JSON.parse(localStorage.getItem('sms_courses_db') || '[]');
        if (isPost) {
            const newCourse = { id: Date.now(), ...bodyData };
            courses.push(newCourse);
            localStorage.setItem('sms_courses_db', JSON.stringify(courses));
            return Promise.resolve({ status: 'success', message: 'Course created successfully', data: newCourse });
        }
        return Promise.resolve({ status: 'success', data: { courses: courses } });
    }

    // Departments Endpoint
    if (endpoint.includes('/departments')) {
        const depts = JSON.parse(localStorage.getItem('sms_depts_db') || '[]');
        return Promise.resolve({ status: 'success', data: { departments: depts } });
    }

    // Timetable Endpoint
    if (endpoint.includes('/timetable')) {
        let timetable = JSON.parse(localStorage.getItem('sms_timetable_db') || '[]');
        if (isPost) {
            const newSlot = { id: Date.now(), ...bodyData };
            timetable.push(newSlot);
            localStorage.setItem('sms_timetable_db', JSON.stringify(timetable));
            return Promise.resolve({ status: 'success', message: 'Slot added', data: newSlot });
        }
        return Promise.resolve({ status: 'success', data: { timetable: timetable } });
    }

    // Exam Schedules Endpoint
    if (endpoint.includes('/exams') || endpoint.includes('/examinations')) {
        let exams = JSON.parse(localStorage.getItem('sms_exams_db') || '[]');
        if (isPost) {
            const newExam = { id: Date.now(), ...bodyData };
            exams.push(newExam);
            localStorage.setItem('sms_exams_db', JSON.stringify(exams));
            return Promise.resolve({ status: 'success', message: 'Exam scheduled', data: newExam });
        }
        return Promise.resolve({ status: 'success', data: { examinations: exams } });
    }

    // Leaves Endpoint
    if (endpoint.includes('/leave')) {
        let leaves = JSON.parse(localStorage.getItem('sms_leaves_db') || '[]');
        if (isPost) {
            const newLeave = {
                id: Date.now(),
                student_id: bodyData.student_id || 2,
                student_name: bodyData.student_name || 'Student',
                roll_no: bodyData.roll_no || 'ECE2026-042',
                department: bodyData.department || 'ECE',
                reason: bodyData.reason,
                leave_type: bodyData.leave_type || 'Casual',
                start_date: bodyData.start_date,
                end_date: bodyData.end_date,
                status: 'Pending'
            };
            leaves.push(newLeave);
            localStorage.setItem('sms_leaves_db', JSON.stringify(leaves));
            return Promise.resolve({ status: 'success', message: 'Leave application submitted', data: newLeave });
        }
        return Promise.resolve({ status: 'success', data: { leave_requests: leaves } });
    }

    // Default Fallback
    return Promise.resolve({ status: 'success', data: {} });
}
