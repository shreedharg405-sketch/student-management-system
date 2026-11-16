<?php
/**
 * HOD Department Subjects API
 * GET /backend/hod/subjects.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStaff();

$sfStmt = $pdo->prepare("SELECT id, department_id FROM staff WHERE user_id = ? LIMIT 1");
$sfStmt->execute([$user['id']]);
$staff = $sfStmt->fetch();

if (!$staff) {
    jsonError('Staff record not found.', 404);
}
$deptId = (int)$staff['department_id'];

$stmt = $pdo->prepare("
    SELECT 
        id,
        subject_code AS code,
        subject_name AS name,
        subject_code,
        subject_name,
        semester,
        credits,
        department_id
    FROM subjects
    WHERE department_id = ?
    ORDER BY subject_code ASC
");
$stmt->execute([$deptId]);
$subjects = $stmt->fetchAll();

echo json_encode([
    'success'  => true,
    'data'     => $subjects,
    'subjects' => $subjects
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;

