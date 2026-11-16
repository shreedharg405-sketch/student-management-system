<?php
/**
 * Staff Classes for Today's Attendance API
 * GET /backend/staff/classes.php
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

$targetDate = $_GET['date'] ?? $_GET['date_str'] ?? date('Y-m-d');
$weekday = date('l', strtotime($targetDate));

// If HOD, allow viewing either all department classes or their own
$isHOD = ($user['role'] === 'HOD');
$filterAll = isset($_GET['all']) && $_GET['all'] === 'true' && $isHOD;

if ($filterAll) {
    $stmt = $pdo->prepare("
        SELECT 
            t.id AS timetable_id,
            t.day,
            t.period,
            t.start_time,
            t.end_time,
            t.room,
            t.year,
            t.section,
            t.batch_id,
            t.department_id,
            t.subject_id,
            sub.subject_code,
            sub.subject_name,
            d.code AS department_code,
            d.name AS department_name,
            b.batch_name,
            u_fac.name AS faculty_name,
            att.id AS attendance_id,
            CASE WHEN att.id IS NOT NULL THEN 1 ELSE 0 END AS is_marked
        FROM timetable t
        JOIN subjects sub ON t.subject_id = sub.id
        JOIN departments d ON t.department_id = d.id
        JOIN staff st ON t.faculty_id = st.id
        JOIN users u_fac ON st.user_id = u_fac.id
        LEFT JOIN batches b ON t.batch_id = b.id
        LEFT JOIN attendance att ON (
            att.date = ? 
            AND att.period = t.period 
            AND att.subject_id = t.subject_id 
            AND (att.batch_id = t.batch_id OR (att.batch_id IS NULL AND t.batch_id IS NULL))
        )
        WHERE t.day = ?
          AND t.department_id = ?
        ORDER BY t.period ASC
    ");
    $stmt->execute([$targetDate, $weekday, $staff['department_id']]);
} else {
    $stmt = $pdo->prepare("
        SELECT 
            t.id AS timetable_id,
            t.day,
            t.period,
            t.start_time,
            t.end_time,
            t.room,
            t.year,
            t.section,
            t.batch_id,
            t.department_id,
            t.subject_id,
            sub.subject_code,
            sub.subject_name,
            d.code AS department_code,
            d.name AS department_name,
            b.batch_name,
            u_fac.name AS faculty_name,
            att.id AS attendance_id,
            CASE WHEN att.id IS NOT NULL THEN 1 ELSE 0 END AS is_marked
        FROM timetable t
        JOIN subjects sub ON t.subject_id = sub.id
        JOIN departments d ON t.department_id = d.id
        JOIN staff st ON t.faculty_id = st.id
        JOIN users u_fac ON st.user_id = u_fac.id
        LEFT JOIN batches b ON t.batch_id = b.id
        LEFT JOIN attendance att ON (
            att.date = ? 
            AND att.period = t.period 
            AND att.subject_id = t.subject_id 
            AND (att.batch_id = t.batch_id OR (att.batch_id IS NULL AND t.batch_id IS NULL))
        )
        WHERE t.day = ?
          AND t.faculty_id = ?
        ORDER BY t.period ASC
    ");
    $stmt->execute([$targetDate, $weekday, $facultyId]);
}

$classes = $stmt->fetchAll();

foreach ($classes as &$c) {
    $c['id'] = (int)$c['timetable_id'];
    $c['date'] = $targetDate;
    $c['department'] = $c['department_code'];
    $c['attendance_marked'] = !empty($c['attendance_id']);
}
unset($c);

echo json_encode([
    'success' => true,
    'data'    => [
        'date'    => $targetDate,
        'day'     => $weekday,
        'count'   => count($classes),
        'classes' => $classes
    ],
    'classes' => $classes,
    'count'   => count($classes),
    'date'    => $targetDate,
    'day'     => $weekday
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
