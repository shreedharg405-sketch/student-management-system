<?php
/**
 * HOD Department Batches API
 * GET /backend/hod/batches.php
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
        year,
        section,
        batch_name,
        department_id
    FROM batches
    WHERE department_id = ?
    ORDER BY year ASC, section ASC, batch_name ASC
");
$stmt->execute([$deptId]);
$batches = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'data'    => $batches,
    'batches' => $batches
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;

