<?php
/**
 * HOD Delete Timetable Slot API
 * POST / DELETE /backend/hod/timetable-delete.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

$input = getRequestData();
$slotId = (int)($input['id'] ?? $_GET['id'] ?? 0);

if ($slotId <= 0) {
    jsonError('Valid Timetable Slot ID is required.', 400);
}

$chkStmt = $pdo->prepare("SELECT id FROM timetable WHERE id = ? LIMIT 1");
$chkStmt->execute([$slotId]);
if (!$chkStmt->fetch()) {
    jsonError('Timetable slot not found.', 404);
}

$delStmt = $pdo->prepare("DELETE FROM timetable WHERE id = ?");
$delStmt->execute([$slotId]);

jsonSuccess(['deleted_id' => $slotId], 200, 'Timetable slot deleted successfully.');
