<?php
/**
 * Student Exams Schedule API
 * GET /backend/student/exams.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStudent();

$stStmt = $pdo->prepare("SELECT department_id, year, section FROM students WHERE user_id = ? LIMIT 1");
$stStmt->execute([$user['id']]);
$student = $stStmt->fetch();

if (!$student) {
    jsonError('Student record not found.', 404);
}

$stmt = $pdo->prepare("
    SELECT 
        e.id,
        e.exam_name,
        e.exam_type,
        e.semester,
        e.date,
        e.start_time,
        e.end_time,
        e.room,
        sub.subject_code,
        sub.subject_name
    FROM exam_schedule e
    JOIN subjects sub ON e.subject_id = sub.id
    WHERE e.department_id = ?
      AND e.year = ?
      AND e.is_published = 1
    ORDER BY e.date ASC, e.start_time ASC
");
$stmt->execute([$student['department_id'], $student['year']]);
$exams = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'data'    => $exams,
    'exams'   => $exams
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
