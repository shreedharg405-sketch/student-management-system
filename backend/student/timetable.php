<?php
/**
 * Student Timetable API
 * GET /backend/student/timetable.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStudent();

$stStmt = $pdo->prepare("SELECT department_id, year, section, batch_id FROM students WHERE user_id = ? LIMIT 1");
$stStmt->execute([$user['id']]);
$student = $stStmt->fetch();

if (!$student) {
    jsonError('Student record not found.', 404);
}

// Fetch timetable matching student's department, year, section, and (batch_id OR common)
$ttStmt = $pdo->prepare("
    SELECT 
        t.id,
        t.day,
        t.period,
        t.start_time,
        t.end_time,
        t.room,
        sub.subject_code,
        sub.subject_name,
        u_fac.name AS faculty_name,
        b.batch_name
    FROM timetable t
    JOIN subjects sub ON t.subject_id = sub.id
    JOIN staff st ON t.faculty_id = st.id
    JOIN users u_fac ON st.user_id = u_fac.id
    LEFT JOIN batches b ON t.batch_id = b.id
    WHERE t.department_id = ?
      AND t.year = ?
      AND t.section = ?
      AND (t.batch_id IS NULL OR t.batch_id = ?)
    ORDER BY 
        FIELD(t.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
        t.period ASC
");
$ttStmt->execute([
    $student['department_id'],
    $student['year'],
    $student['section'],
    $student['batch_id']
]);
$slots = $ttStmt->fetchAll();

// Group by day for convenience
$byDay = [
    'Monday'    => [],
    'Tuesday'   => [],
    'Wednesday' => [],
    'Thursday'  => [],
    'Friday'    => [],
    'Saturday'  => []
];

foreach ($slots as $s) {
    if (isset($byDay[$s['day']])) {
        $byDay[$s['day']][] = $s;
    }
}

echo json_encode([
    'success'   => true,
    'data'      => [
        'all_slots' => $slots,
        'schedule'  => $byDay
    ],
    'all_slots' => $slots,
    'schedule'  => $byDay,
    'timetable' => $slots
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
