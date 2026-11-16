<?php
/**
 * Student Attendance API
 * GET /backend/student/attendance.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStudent();

// Fetch student ID
$stStmt = $pdo->prepare("SELECT id, department_id, batch_id FROM students WHERE user_id = ? LIMIT 1");
$stStmt->execute([$user['id']]);
$student = $stStmt->fetch();

if (!$student) {
    jsonError('Student record not found.', 404);
}
$studentId = (int)$student['id'];

// Subject-wise stats query
$subStmt = $pdo->prepare("
    SELECT 
        sub.id AS subject_id,
        sub.subject_code,
        sub.subject_name,
        COUNT(ar.id) AS total_classes,
        SUM(CASE WHEN ar.status = 'Present' THEN 1 ELSE 0 END) AS attended_classes,
        SUM(CASE WHEN ar.status = 'Absent' THEN 1 ELSE 0 END) AS absent_classes
    FROM subjects sub
    LEFT JOIN attendance a ON sub.id = a.subject_id
    LEFT JOIN attendance_records ar ON a.id = ar.attendance_id AND ar.student_id = ?
    WHERE sub.department_id = ?
    GROUP BY sub.id, sub.subject_code, sub.subject_name
    ORDER BY sub.subject_code ASC
");
$subStmt->execute([$studentId, $student['department_id']]);
$subjectsData = $subStmt->fetchAll();

$subjectList = [];
$totalAll = 0;
$attendedAll = 0;

foreach ($subjectsData as $row) {
    $tot = (int)$row['total_classes'];
    $att = (int)$row['attended_classes'];
    $abs = (int)$row['absent_classes'];
    $pct = $tot > 0 ? round(($att / $tot) * 100, 1) : 100.0;
    
    $totalAll += $tot;
    $attendedAll += $att;

    $subjectList[] = [
        'subject_id'     => (int)$row['subject_id'],
        'subject_code'   => $row['subject_code'],
        'subject_name'   => $row['subject_name'],
        'faculty_name'   => 'Faculty Staff',
        'total_classes'  => $tot,
        'attended'       => $att,
        'present'        => $att,
        'absent'         => $abs,
        'percentage'     => $pct,
        'status_color'   => $pct >= 85 ? 'success' : ($pct >= 75 ? 'warning' : 'danger')
    ];
}

$overallPct = $totalAll > 0 ? round(($attendedAll / $totalAll) * 100, 1) : 100.0;

// Recent attendance records
$recStmt = $pdo->prepare("
    SELECT 
        a.date,
        a.period,
        sub.subject_code,
        sub.subject_name,
        ar.status,
        ar.remarks,
        u_fac.name AS faculty_name
    FROM attendance_records ar
    JOIN attendance a ON ar.attendance_id = a.id
    JOIN subjects sub ON a.subject_id = sub.id
    JOIN staff st ON a.faculty_id = st.id
    JOIN users u_fac ON st.user_id = u_fac.id
    WHERE ar.student_id = ?
    ORDER BY a.date DESC, a.period DESC
    LIMIT 30
");
$recStmt->execute([$studentId]);
$recentRecords = $recStmt->fetchAll();

$overallData = [
    'total_classes'    => $totalAll,
    'attended_classes' => $attendedAll,
    'absent_classes'   => $totalAll - $attendedAll,
    'percentage'       => $overallPct,
    'eligibility'      => $overallPct >= 75 ? 'Eligible for Examinations' : 'Low Attendance Alert'
];

echo json_encode([
    'success'            => true,
    'data'               => [
        'overall'  => $overallData,
        'subjects' => $subjectList,
        'history'  => $recentRecords
    ],
    'overall'            => $overallData,
    'overall_percentage' => $overallPct,
    'subjects'           => $subjectList,
    'history'            => $recentRecords
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
