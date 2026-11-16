<?php
/**
 * HOD Update Timetable Slot API
 * POST / PUT /backend/hod/timetable-update.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

$input = getRequestData();
$slotId = (int)($input['id'] ?? $_GET['id'] ?? 0);

if ($slotId <= 0) {
    jsonError('Valid Timetable Slot ID is required.', 400);
}

// Fetch existing slot
$chkStmt = $pdo->prepare("SELECT * FROM timetable WHERE id = ? LIMIT 1");
$chkStmt->execute([$slotId]);
$existing = $chkStmt->fetch();

if (!$existing) {
    jsonError('Timetable slot not found.', 404);
}

$day          = trim($input['day'] ?? $existing['day']);
$period       = (int)($input['period'] ?? $existing['period']);
$startTime    = trim($input['start_time'] ?? $existing['start_time']);
$endTime      = trim($input['end_time'] ?? $existing['end_time']);
$departmentId = (int)($input['department_id'] ?? $existing['department_id']);
$year         = trim($input['year'] ?? $existing['year']);
$section      = trim($input['section'] ?? $existing['section']);
$batchId      = isset($input['batch_id']) ? (!empty($input['batch_id']) ? (int)$input['batch_id'] : null) : $existing['batch_id'];
$subjectId    = (int)($input['subject_id'] ?? $existing['subject_id']);
$facultyId    = (int)($input['faculty_id'] ?? $existing['faculty_id']);
$room         = trim($input['room'] ?? $existing['room']);

// 1. Conflict Check: Same Faculty + Same Day + Same Period (excluding this slot)
$facStmt = $pdo->prepare("
    SELECT t.id, t.room, sub.subject_name, u.name AS faculty_name
    FROM timetable t
    JOIN subjects sub ON t.subject_id = sub.id
    JOIN staff st ON t.faculty_id = st.id
    JOIN users u ON st.user_id = u.id
    WHERE t.day = ? AND t.period = ? AND t.faculty_id = ? AND t.id != ?
    LIMIT 1
");
$facStmt->execute([$day, $period, $facultyId, $slotId]);
$facConflict = $facStmt->fetch();
if ($facConflict) {
    jsonError("Scheduling Conflict: {$facConflict['faculty_name']} is already assigned to {$facConflict['room']} on {$day} Period {$period}.", 400);
}

// 2. Conflict Check: Same Room + Same Day + Same Period (excluding this slot)
$roomStmt = $pdo->prepare("
    SELECT t.id, sub.subject_name
    FROM timetable t
    JOIN subjects sub ON t.subject_id = sub.id
    WHERE t.day = ? AND t.period = ? AND t.room = ? AND t.id != ?
    LIMIT 1
");
$roomStmt->execute([$day, $period, $room, $slotId]);
$roomConflict = $roomStmt->fetch();
if ($roomConflict) {
    jsonError("Scheduling Conflict: Room '{$room}' is already booked on {$day} Period {$period} for {$roomConflict['subject_name']}.", 400);
}

// 3. Conflict Check: Same Batch/Section + Same Day + Same Period (excluding this slot)
if ($batchId !== null) {
    $batchStmt = $pdo->prepare("
        SELECT t.id, sub.subject_name
        FROM timetable t
        JOIN subjects sub ON t.subject_id = sub.id
        WHERE t.day = ? AND t.period = ? AND t.department_id = ? AND t.year = ? AND t.section = ?
          AND (t.batch_id = ? OR t.batch_id IS NULL) AND t.id != ?
        LIMIT 1
    ");
    $batchStmt->execute([$day, $period, $departmentId, $year, $section, $batchId, $slotId]);
    $batchConflict = $batchStmt->fetch();
    if ($batchConflict) {
        jsonError("Scheduling Conflict: This batch already has '{$batchConflict['subject_name']}' scheduled on {$day} Period {$period}.", 400);
    }
}

// Update slot
$upStmt = $pdo->prepare("
    UPDATE timetable 
    SET day = ?, period = ?, start_time = ?, end_time = ?, department_id = ?, 
        year = ?, section = ?, batch_id = ?, subject_id = ?, faculty_id = ?, room = ?
    WHERE id = ?
");
$upStmt->execute([
    $day, $period, $startTime, $endTime, $departmentId,
    $year, $section, $batchId, $subjectId, $facultyId, $room,
    $slotId
]);

jsonSuccess([
    'id'      => $slotId,
    'day'     => $day,
    'period'  => $period,
    'room'    => $room
], 200, 'Timetable slot updated successfully.');
