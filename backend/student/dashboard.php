<?php
/**
 * Student Dashboard Overview API
 * GET /backend/student/dashboard.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStudent();

$stmt = $pdo->prepare("
    SELECT 
        s.id AS student_id,
        s.register_number,
        s.year,
        s.section,
        s.batch_id,
        s.department_id,
        u.name AS student_name,
        d.name AS department_name,
        b.batch_name,
        u_mentor.name AS mentor_name,
        u_hod.name AS hod_name
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN departments d ON s.department_id = d.id
    LEFT JOIN batches b ON s.batch_id = b.id
    LEFT JOIN staff st_mentor ON s.mentor_id = st_mentor.id
    LEFT JOIN users u_mentor ON st_mentor.user_id = u_mentor.id
    LEFT JOIN staff st_hod ON s.hod_id = st_hod.id
    LEFT JOIN users u_hod ON st_hod.user_id = u_hod.id
    WHERE s.user_id = ?
    LIMIT 1
");
$stmt->execute([$user['id']]);
$student = $stmt->fetch();

if (!$student) {
    jsonError('Student record not found.', 404);
}

// 1. Overall Attendance %
$attStmt = $pdo->prepare("
    SELECT 
        COUNT(ar.id) AS total_classes,
        SUM(CASE WHEN ar.status = 'Present' THEN 1 ELSE 0 END) AS attended_classes
    FROM attendance_records ar
    WHERE ar.student_id = ?
");
$attStmt->execute([$student['student_id']]);
$attRow = $attStmt->fetch();
$totalCls = (int)($attRow['total_classes'] ?? 0);
$attendedCls = (int)($attRow['attended_classes'] ?? 0);
$attPct = $totalCls > 0 ? round(($attendedCls / $totalCls) * 100, 1) : 100.0;

// 2. Next class / upcoming period today
$today = date('l');
$nextClsStmt = $pdo->prepare("
    SELECT t.period, t.start_time, t.end_time, t.room, sub.subject_code, sub.subject_name
    FROM timetable t
    JOIN subjects sub ON t.subject_id = sub.id
    WHERE t.department_id = ? AND t.year = ? AND t.section = ?
      AND (t.batch_id IS NULL OR t.batch_id = ?)
      AND t.day = ?
    ORDER BY t.period ASC
    LIMIT 1
");
$nextClsStmt->execute([
    $student['department_id'],
    $student['year'],
    $student['section'],
    $student['batch_id'],
    $today
]);
$nextCls = $nextClsStmt->fetch();
$nextClassStr = $nextCls 
    ? "Period {$nextCls['period']}: {$nextCls['subject_code']} ({$nextCls['room']})" 
    : "No more classes today";

// 3. Next upcoming exam
$exStmt = $pdo->prepare("
    SELECT e.exam_name, e.date, sub.subject_code
    FROM exam_schedule e
    JOIN subjects sub ON e.subject_id = sub.id
    WHERE e.department_id = ? AND e.year = ? AND e.is_published = 1
      AND e.date >= CURDATE()
    ORDER BY e.date ASC, e.start_time ASC
    LIMIT 1
");
$exStmt->execute([$student['department_id'], $student['year']]);
$nextExam = $exStmt->fetch();
$nextExamStr = $nextExam 
    ? "{$nextExam['date']} - {$nextExam['subject_code']}" 
    : "No upcoming exams";

// 4. Pending leaves
$lvStmt = $pdo->prepare("
    SELECT COUNT(*) AS pending_count 
    FROM leave_requests 
    WHERE student_id = ? AND status LIKE 'PENDING%'
");
$lvStmt->execute([$student['student_id']]);
$pendingCount = (int)$lvStmt->fetch()['pending_count'];
$pendingLeavesStr = "{$pendingCount} Pending Approval";

$info = [
    'name'            => $student['student_name'],
    'register_number' => $student['register_number'],
    'department'      => $student['department_name'],
    'year'            => $student['year'],
    'section'         => $student['section'],
    'batch'           => $student['batch_name'] ?: 'Batch 1',
    'mentor'          => $student['mentor_name'] ?: 'Not Assigned',
    'hod'             => $student['hod_name'] ?: 'HOD'
];

$cards = [
    'attendance_pct'  => $attPct,
    'next_class'      => $nextClassStr,
    'upcoming_exam'   => $nextExamStr,
    'pending_leaves'  => $pendingLeavesStr
];

echo json_encode([
    'success'      => true,
    'data'         => [
        'student_info' => $info,
        'cards'        => $cards
    ],
    'student_info' => $info,
    'cards'        => $cards
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;

