<?php
/**
 * HOD Staff Governance & Faculty Directory API
 * GET / POST /backend/hod/staff.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

$sfStmt = $pdo->prepare("SELECT id, department_id FROM staff WHERE user_id = ? LIMIT 1");
$sfStmt->execute([$user['id']]);
$staff = $sfStmt->fetch();

if (!$staff) {
    jsonError('HOD profile not found.', 404);
}
$deptId = (int)$staff['department_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("
        SELECT 
            st.id AS staff_id,
            st.id AS id,
            st.user_id,
            st.staff_code,
            st.designation,
            st.phone,
            st.qualification,
            st.joining_date,
            u.name AS faculty_name,
            u.name AS name,
            u.email,
            u.role,
            u.is_active,
            d.name AS department_name,
            d.code AS department_code,
            (SELECT COUNT(*) FROM students s WHERE s.mentor_id = st.id) AS mentee_count,
            (SELECT COUNT(*) FROM timetable t WHERE t.faculty_id = st.id) AS assigned_periods
        FROM staff st
        JOIN users u ON st.user_id = u.id
        LEFT JOIN departments d ON st.department_id = d.id
        WHERE st.department_id = ?
        ORDER BY FIELD(u.role, 'HOD', 'ASSISTANT_PROFESSOR'), u.name ASC
    ");
    $stmt->execute([$deptId]);
    $facultyList = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data'    => $facultyList,
        'staff'   => $facultyList
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getRequestData();
    $name          = trim($input['name'] ?? '');
    $email         = trim($input['email'] ?? '');
    $password      = $input['password'] ?? 'staff123';
    $designation   = trim($input['designation'] ?? 'Assistant Professor');
    $phone         = trim($input['phone'] ?? '');
    $qualification = trim($input['qualification'] ?? '');
    $staffCode     = trim($input['staff_code'] ?? '');

    if (empty($name) || empty($email) || empty($staffCode)) {
        jsonError('Name, Email, and Staff Code are required.', 400);
    }

    // Check email uniqueness
    $chkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $chkEmail->execute([$email]);
    if ($chkEmail->fetch()) {
        jsonError('User with this email already exists.', 400);
    }

    try {
        $pdo->beginTransaction();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insUser = $pdo->prepare("
            INSERT INTO users (name, email, password_hash, role, is_active)
            VALUES (?, ?, ?, 'ASSISTANT_PROFESSOR', 1)
        ");
        $insUser->execute([$name, $email, $hash]);
        $newUserId = (int)$pdo->lastInsertId();

        $insStaff = $pdo->prepare("
            INSERT INTO staff (user_id, staff_code, department_id, designation, phone, qualification, joining_date)
            VALUES (?, ?, ?, ?, ?, ?, CURDATE())
        ");
        $insStaff->execute([$newUserId, $staffCode, $deptId, $designation, $phone, $qualification]);
        $newStaffId = (int)$pdo->lastInsertId();

        $pdo->commit();

        jsonSuccess([
            'staff_id' => $newStaffId,
            'user_id'  => $newUserId,
            'name'     => $name,
            'email'    => $email
        ], 201, 'Faculty member added successfully.');

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonError('Failed to create faculty member: ' . $e->getMessage(), 500);
    }
}
