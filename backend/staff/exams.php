<?php
/**
 * Staff Exams Schedule API
 * GET /backend/staff/exams.php
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
$deptId = (int)$staff['department_id'];

$stmt = $pdo->prepare("
    SELECT 
        e.id,
        e.exam_name,
        e.exam_type,
        e.year,
        e.section,
        e.semester,
        e.date,
        e.start_time,
        e.end_time,
        e.room,
        e.is_published,
        sub.subject_code,
        sub.subject_name
    FROM exam_schedule e
    JOIN subjects sub ON e.subject_id = sub.id
    WHERE e.department_id = ?
    ORDER BY e.date ASC, e.start_time ASC
");
$stmt->execute([$deptId]);
$exams = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'data'    => $exams,
    'exams'   => $exams
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
