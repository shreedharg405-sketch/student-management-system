<?php
/**
 * HOD Department Dashboard Overview API
 * GET /backend/hod/dashboard.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

$sfStmt = $pdo->prepare("SELECT id, department_id FROM staff WHERE user_id = ? LIMIT 1");
$sfStmt->execute([$user['id']]);
$staff = $sfStmt->fetch();

if (!$staff) {
    jsonError('HOD staff record not found.', 404);
}
$deptId = (int)$staff['department_id'];

// 1. Total Students
$stStmt = $pdo->prepare("SELECT COUNT(*) AS total_students FROM students WHERE department_id = ?");
$stStmt->execute([$deptId]);
$totalStudents = (int)$stStmt->fetch()['total_students'];

// 2. Total Faculty
$facStmt = $pdo->prepare("SELECT COUNT(*) AS total_faculty FROM staff WHERE department_id = ?");
$facStmt->execute([$deptId]);
$totalFaculty = (int)$facStmt->fetch()['total_faculty'];

// 3. Pending Leaves for HOD
$lvStmt = $pdo->prepare("
    SELECT COUNT(*) AS pending_leaves 
    FROM leave_requests lr
    JOIN students s ON lr.student_id = s.id
    WHERE s.department_id = ? AND lr.status = 'PENDING_HOD'
");
$lvStmt->execute([$deptId]);
$pendingLeaves = (int)$lvStmt->fetch()['pending_leaves'];

// 4. Department Attendance Average
$attStmt = $pdo->prepare("
    SELECT 
        COUNT(ar.id) AS total_records,
        SUM(CASE WHEN ar.status = 'Present' THEN 1 ELSE 0 END) AS present_records
    FROM attendance_records ar
    JOIN attendance a ON ar.attendance_id = a.id
    JOIN subjects sub ON a.subject_id = sub.id
    WHERE sub.department_id = ?
");
$attStmt->execute([$deptId]);
$attData = $attStmt->fetch();
$totalAtt = (int)$attData['total_records'];
$presentAtt = (int)$attData['present_records'];
$attRate = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 100.0;

// 5. Total Timetable Slots
$ttStmt = $pdo->prepare("SELECT COUNT(*) AS total_slots FROM timetable WHERE department_id = ?");
$ttStmt->execute([$deptId]);
$totalSlots = (int)$ttStmt->fetch()['total_slots'];

// 6. Upcoming Published Exams
$exStmt = $pdo->prepare("SELECT COUNT(*) AS total_exams FROM exam_schedule WHERE department_id = ? AND is_published = 1");
$exStmt->execute([$deptId]);
$totalExams = (int)$exStmt->fetch()['total_exams'];

// 7. Recent Notices
$noticesStmt = $pdo->prepare("SELECT * FROM notices WHERE department_id = ? OR target_role = 'ALL' ORDER BY created_at DESC LIMIT 5");
$noticesStmt->execute([$deptId]);
$notices = $noticesStmt->fetchAll();

jsonSuccess([
    'stats' => [
        'total_students'      => $totalStudents,
        'total_faculty'       => $totalFaculty,
        'pending_leaves'      => $pendingLeaves,
        'attendance_rate'     => $attRate,
        'timetable_slots'     => $totalSlots,
        'scheduled_exams'     => $totalExams
    ],
    'notices' => $notices
]);
