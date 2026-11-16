<?php
/**
 * HOD Update Exam Schedule API
 * POST / PUT /backend/hod/exam-update.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

$input = getRequestData();
$examId = (int)($input['id'] ?? $_GET['id'] ?? 0);

if ($examId <= 0) {
    jsonError('Valid Exam Schedule ID is required.', 400);
}

$chkStmt = $pdo->prepare("SELECT * FROM exam_schedule WHERE id = ? LIMIT 1");
$chkStmt->execute([$examId]);
$existing = $chkStmt->fetch();

if (!$existing) {
    jsonError('Exam schedule not found.', 404);
}

$examName     = trim($input['exam_name'] ?? $existing['exam_name']);
$examType     = trim($input['exam_type'] ?? $existing['exam_type']);
$subjectId    = (int)($input['subject_id'] ?? $existing['subject_id']);
$departmentId = (int)($input['department_id'] ?? $existing['department_id']);
$year         = trim($input['year'] ?? $existing['year']);
$section      = trim($input['section'] ?? $existing['section']);
$semester     = trim($input['semester'] ?? $existing['semester']);
$date         = trim($input['date'] ?? $existing['date']);
$startTime    = trim($input['start_time'] ?? $existing['start_time']);
$endTime      = trim($input['end_time'] ?? $existing['end_time']);
$room         = trim($input['room'] ?? $existing['room']);
$isPublished  = isset($input['is_published']) ? (int)$input['is_published'] : $existing['is_published'];

$upStmt = $pdo->prepare("
    UPDATE exam_schedule 
    SET exam_name = ?, exam_type = ?, subject_id = ?, department_id = ?, year = ?, 
        section = ?, semester = ?, date = ?, start_time = ?, end_time = ?, room = ?, is_published = ?
    WHERE id = ?
");
$upStmt->execute([
    $examName, $examType, $subjectId, $departmentId, $year,
    $section, $semester, $date, $startTime, $endTime, $room, $isPublished,
    $examId
]);

jsonSuccess([
    'id'           => $examId,
    'exam_name'    => $examName,
    'is_published' => $isPublished
], 200, 'Exam schedule updated successfully.');
