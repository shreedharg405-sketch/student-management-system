<?php
/**
 * Staff Students Roster API
 * GET /backend/staff/students.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStaff();

$sfStmt = $pdo->prepare("SELECT id, department_id FROM staff WHERE user_id = ? LIMIT 1");
$sfStmt->execute([$user['id']]);
$staff = $sfStmt->fetch();

if (!$staff) {
    jsonError('Faculty record not found.', 404);
}
$staffId = (int)$staff['id'];
$deptId  = (int)$staff['department_id'];
$isHOD   = ($user['role'] === 'HOD');

$batchId = !empty($_GET['batch_id']) ? (int)$_GET['batch_id'] : null;
$deptParam = !empty($_GET['department_id']) ? (int)$_GET['department_id'] : $deptId;

$query = "
    SELECT 
        s.id AS student_id,
        s.id,
        s.register_number,
        s.year,
        s.section,
        s.phone,
        s.gender,
        s.status,
        u.id AS user_id,
        u.name AS student_name,
        u.name AS name,
        u.email AS student_email,
        u.email AS email,
        d.name AS department_name,
        d.code AS department_code,
        d.code AS department,
        b.id AS batch_id,
        b.batch_name,
        b.batch_name AS batch,
        u_mentor.name AS mentor_name
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN departments d ON s.department_id = d.id
    LEFT JOIN batches b ON s.batch_id = b.id
    LEFT JOIN staff st_mentor ON s.mentor_id = st_mentor.id
    LEFT JOIN users u_mentor ON st_mentor.user_id = u_mentor.id
    WHERE 1=1
";
$params = [];

if ($batchId !== null) {
    $query .= " AND s.batch_id = ?";
    $params[] = $batchId;
} else if (!$isHOD && !isset($_GET['all'])) {
    // If regular staff and not filtering by batch, show mentees or department students
    $query .= " AND (s.mentor_id = ? OR s.department_id = ?)";
    $params[] = $staffId;
    $params[] = $deptParam;
} else {
    $query .= " AND s.department_id = ?";
    $params[] = $deptParam;
}

$query .= " ORDER BY s.register_number ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();

echo json_encode([
    'success'  => true,
    'data'     => $students,
    'students' => $students
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
