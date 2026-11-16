<?php
/**
 * Staff Timetable API
 * GET /backend/staff/timetable.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStaff();

$sfStmt = $pdo->prepare("SELECT id, department_id FROM staff WHERE user_id = ? LIMIT 1");
$sfStmt->execute([$user['id']]);
$staff = $sfStmt->fetch();

if (!$staff) {
    jsonError('Faculty record not found.', 404);
}
$facultyId = (int)$staff['id'];
$deptId = (int)$staff['department_id'];
$isHOD = ($user['role'] === 'HOD');

$showAll = $isHOD && isset($_GET['all']) && $_GET['all'] === 'true';

if ($showAll) {
    $stmt = $pdo->prepare("
        SELECT 
            t.id,
            t.day,
            t.period,
            t.start_time,
            t.end_time,
            t.year,
            t.section,
            t.room,
            sub.id AS subject_id,
            sub.subject_code,
            sub.subject_name,
            u_fac.name AS faculty_name,
            st.staff_code,
            b.batch_name
        FROM timetable t
        JOIN subjects sub ON t.subject_id = sub.id
        JOIN staff st ON t.faculty_id = st.id
        JOIN users u_fac ON st.user_id = u_fac.id
        LEFT JOIN batches b ON t.batch_id = b.id
        WHERE t.department_id = ?
        ORDER BY 
            FIELD(t.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
            t.period ASC
    ");
    $stmt->execute([$deptId]);
} else {
    $stmt = $pdo->prepare("
        SELECT 
            t.id,
            t.day,
            t.period,
            t.start_time,
            t.end_time,
            t.year,
            t.section,
            t.room,
            sub.id AS subject_id,
            sub.subject_code,
            sub.subject_name,
            u_fac.name AS faculty_name,
            st.staff_code,
            b.batch_name
        FROM timetable t
        JOIN subjects sub ON t.subject_id = sub.id
        JOIN staff st ON t.faculty_id = st.id
        JOIN users u_fac ON st.user_id = u_fac.id
        LEFT JOIN batches b ON t.batch_id = b.id
        WHERE t.faculty_id = ?
        ORDER BY 
            FIELD(t.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
            t.period ASC
    ");
    $stmt->execute([$facultyId]);
}

$slots = $stmt->fetchAll();

echo json_encode([
    'success'   => true,
    'data'      => $slots,
    'timetable' => $slots,
    'slots'     => $slots
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
