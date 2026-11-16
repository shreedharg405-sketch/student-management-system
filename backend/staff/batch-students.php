<?php
/**
 * Staff Batch Students for Attendance API
 * GET /backend/staff/batch-students.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStaff();

$batchId = (int)($_GET['batch_id'] ?? 0);
$attId = isset($_GET['attendance_id']) && !empty($_GET['attendance_id']) ? (int)$_GET['attendance_id'] : 0;

if ($batchId <= 0) {
    jsonError('Valid Batch ID is required.', 400);
}

$stmt = $pdo->prepare("
    SELECT 
        s.id AS student_id,
        s.id,
        s.register_number,
        u.name,
        u.email,
        COALESCE(ar.status, 'Present') AS status,
        ar.remarks
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN attendance_records ar ON ar.student_id = s.id AND ar.attendance_id = ?
    WHERE s.batch_id = ?
    ORDER BY s.register_number ASC
");
$stmt->execute([$attId, $batchId]);
$students = $stmt->fetchAll();

echo json_encode([
    'success'  => true,
    'data'     => $students,
    'students' => $students
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;

