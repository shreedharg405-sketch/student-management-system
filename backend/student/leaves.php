<?php
/**
 * Student Leave History API
 * GET /backend/student/leaves.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStudent();

$stStmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ? LIMIT 1");
$stStmt->execute([$user['id']]);
$student = $stStmt->fetch();

if (!$student) {
    jsonError('Student record not found.', 404);
}
$studentId = (int)$student['id'];

$stmt = $pdo->prepare("
    SELECT 
        lr.id,
        lr.leave_type,
        lr.from_date,
        lr.to_date,
        DATEDIFF(lr.to_date, lr.from_date) + 1 AS duration_days,
        lr.reason,
        lr.attachment_url,
        lr.status,
        lr.created_at,
        lr.updated_at
    FROM leave_requests lr
    WHERE lr.student_id = ?
    ORDER BY lr.created_at DESC
");
$stmt->execute([$studentId]);
$leaves = $stmt->fetchAll();

// Fetch responses for these leaves
foreach ($leaves as &$lv) {
    $respStmt = $pdo->prepare("
        SELECT 
            resp.stage,
            resp.action,
            resp.remarks,
            resp.created_at,
            u.name AS responder_name,
            u.role AS responder_role
        FROM leave_responses resp
        JOIN users u ON resp.responder_id = u.id
        WHERE resp.leave_request_id = ?
        ORDER BY resp.created_at ASC
    ");
    $respStmt->execute([$lv['id']]);
    $lv['responses'] = $respStmt->fetchAll();
}
unset($lv);

echo json_encode([
    'success' => true,
    'data'    => $leaves,
    'leaves'  => $leaves
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
