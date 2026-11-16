<?php
/**
 * HOD Departments Governance API
 * GET /backend/hod/departments.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireHOD();

$stmt = $pdo->query("
    SELECT 
        d.id,
        d.name,
        d.code,
        d.created_at,
        (SELECT COUNT(*) FROM staff st WHERE st.department_id = d.id) AS faculty_count,
        (SELECT COUNT(*) FROM students s WHERE s.department_id = d.id) AS student_count,
        (SELECT COUNT(*) FROM courses c WHERE c.department_id = d.id) AS course_count,
        (SELECT COUNT(*) FROM subjects sub WHERE sub.department_id = d.id) AS subject_count
    FROM departments d
    ORDER BY d.id ASC
");
$departments = $stmt->fetchAll();

echo json_encode([
    'success'     => true,
    'data'        => $departments,
    'departments' => $departments
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
