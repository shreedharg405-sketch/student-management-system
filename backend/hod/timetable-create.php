<?php
/**
 * HOD Create Timetable Slot API
 * POST /backend/hod/timetable-create.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed. Only POST is accepted.', 405);
}

$input = getRequestData();

$day          = trim($input['day'] ?? '');
$period       = (int)($input['period'] ?? 0);
$startTime    = trim($input['start_time'] ?? '');
$endTime      = trim($input['end_time'] ?? '');
$departmentId = (int)($input['department_id'] ?? $_SESSION['department_id'] ?? 1);
$year         = trim($input['year'] ?? '3rd Year');
$section      = trim($input['section'] ?? 'A');
$batchId      = !empty($input['batch_id']) ? (int)$input['batch_id'] : null;
$subjectId    = (int)($input['subject_id'] ?? 0);
$facultyId    = (int)($input['faculty_id'] ?? 0);
$room         = trim($input['room'] ?? '');

if (empty($day) || $period <= 0 || empty($startTime) || empty($endTime) || $subjectId <= 0 || $facultyId <= 0 || empty($room)) {
    jsonError('All required fields (Day, Period, Start Time, End Time, Subject, Faculty, Room) must be filled.', 400);
}

// 1. Conflict Check: Same Faculty + Same Day + Same Period
$facStmt = $pdo->prepare("
    SELECT t.id, t.room, sub.subject_name, u.name AS faculty_name
    FROM timetable t
    JOIN subjects sub ON t.subject_id = sub.id
    JOIN staff st ON t.faculty_id = st.id
    JOIN users u ON st.user_id = u.id
    WHERE t.day = ? AND t.period = ? AND t.faculty_id = ?
    LIMIT 1
");
$facStmt->execute([$day, $period, $facultyId]);
$facConflict = $facStmt->fetch();
if ($facConflict) {
    jsonError("Scheduling Conflict: {$facConflict['faculty_name']} is already assigned to {$facConflict['room']} on {$day} Period {$period}.", 400);
}

// 2. Conflict Check: Same Room + Same Day + Same Period
$roomStmt = $pdo->prepare("
    SELECT t.id, sub.subject_name
    FROM timetable t
    JOIN subjects sub ON t.subject_id = sub.id
    WHERE t.day = ? AND t.period = ? AND t.room = ?
    LIMIT 1
");
$roomStmt->execute([$day, $period, $room]);
$roomConflict = $roomStmt->fetch();
if ($roomConflict) {
    jsonError("Scheduling Conflict: Room '{$room}' is already booked on {$day} Period {$period} for {$roomConflict['subject_name']}.", 400);
}

// 3. Conflict Check: Same Batch/Section + Same Day + Same Period
if ($batchId !== null) {
    $batchStmt = $pdo->prepare("
        SELECT t.id, sub.subject_name
        FROM timetable t
        JOIN subjects sub ON t.subject_id = sub.id
        WHERE t.day = ? AND t.period = ? AND t.department_id = ? AND t.year = ? AND t.section = ?
          AND (t.batch_id = ? OR t.batch_id IS NULL)
        LIMIT 1
    ");
    $batchStmt->execute([$day, $period, $departmentId, $year, $section, $batchId]);
    $batchConflict = $batchStmt->fetch();
    if ($batchConflict) {
        jsonError("Scheduling Conflict: This batch already has '{$batchConflict['subject_name']}' scheduled on {$day} Period {$period}.", 400);
    }
}

// Insert new timetable slot
$insStmt = $pdo->prepare("
    INSERT INTO timetable (day, period, start_time, end_time, department_id, year, section, batch_id, subject_id, faculty_id, room)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$insStmt->execute([
    $day,
    $period,
    $startTime,
    $endTime,
    $departmentId,
    $year,
    $section,
    $batchId,
    $subjectId,
    $facultyId,
    $room
]);

$slotId = (int)$pdo->lastInsertId();

jsonSuccess([
    'id'      => $slotId,
    'day'     => $day,
    'period'  => $period,
    'room'    => $room
], 201, 'Timetable period successfully scheduled without conflicts.');
