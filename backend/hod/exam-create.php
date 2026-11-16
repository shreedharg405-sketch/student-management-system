<?php
/**
 * HOD Create Exam Schedule API
 * POST /backend/hod/exam-create.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed. Only POST is accepted.', 405);
}

$input = getRequestData();

$examName     = trim($input['exam_name'] ?? '');
$examType     = trim($input['exam_type'] ?? 'Theory');
$subjectId    = (int)($input['subject_id'] ?? 0);
$departmentId = (int)($input['department_id'] ?? $_SESSION['department_id'] ?? 1);
$year         = trim($input['year'] ?? '3rd Year');
$section      = trim($input['section'] ?? 'A');
$semester     = trim($input['semester'] ?? '5th Semester');
$date         = trim($input['date'] ?? '');
$startTime    = trim($input['start_time'] ?? '');
$endTime      = trim($input['end_time'] ?? '');
$room         = trim($input['room'] ?? '');
$isPublished  = isset($input['is_published']) ? (int)$input['is_published'] : 1;

if (empty($examName) || $subjectId <= 0 || empty($date) || empty($startTime) || empty($endTime) || empty($room)) {
    jsonError('All required fields (Exam Name, Subject, Date, Start Time, End Time, Room) must be filled.', 400);
}

$stmt = $pdo->prepare("
    INSERT INTO exam_schedule (exam_name, exam_type, subject_id, department_id, year, section, semester, date, start_time, end_time, room, is_published, created_by)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $examName,
    $examType,
    $subjectId,
    $departmentId,
    $year,
    $section,
    $semester,
    $date,
    $startTime,
    $endTime,
    $room,
    $isPublished,
    $user['name']
]);

$examId = (int)$pdo->lastInsertId();

jsonSuccess([
    'id'        => $examId,
    'exam_name' => $examName,
    'date'      => $date
], 201, 'Exam schedule successfully created.');
