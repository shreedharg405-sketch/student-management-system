<?php
/**
 * HOD Delete Exam Schedule API
 * POST / DELETE /backend/hod/exam-delete.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

$input = getRequestData();
$examId = (int)($input['id'] ?? $_GET['id'] ?? 0);

if ($examId <= 0) {
    jsonError('Valid Exam Schedule ID is required.', 400);
}

$chkStmt = $pdo->prepare("SELECT id FROM exam_schedule WHERE id = ? LIMIT 1");
$chkStmt->execute([$examId]);
if (!$chkStmt->fetch()) {
    jsonError('Exam schedule not found.', 404);
}

$delStmt = $pdo->prepare("DELETE FROM exam_schedule WHERE id = ?");
$delStmt->execute([$examId]);

jsonSuccess(['deleted_id' => $examId], 200, 'Exam schedule deleted successfully.');
