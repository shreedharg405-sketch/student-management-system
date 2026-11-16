<?php
/**
 * Student Profile API
 * GET /backend/student/profile.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStudent();

$stmt = $pdo->prepare("
    SELECT 
        s.id AS student_id,
        s.id,
        s.register_number,
        s.year,
        s.section,
        s.dob,
        s.gender,
        s.phone,
        s.address,
        s.status,
        u.name AS student_name,
        u.name AS name,
        u.email AS student_email,
        u.email AS email,
        d.id AS department_id,
        d.name AS department_name,
        d.name AS department,
        d.code AS department_code,
        c.name AS course_name,
        c.name AS course,
        c.code AS course_code,
        b.id AS batch_id,
        b.batch_name,
        b.batch_name AS batch,
        u_mentor.name AS mentor_name,
        st_mentor.phone AS mentor_phone,
        u_mentor.email AS mentor_email,
        u_hod.name AS hod_name,
        u_hod.email AS hod_email
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN departments d ON s.department_id = d.id
    LEFT JOIN courses c ON s.course_id = c.id
    LEFT JOIN batches b ON s.batch_id = b.id
    LEFT JOIN staff st_mentor ON s.mentor_id = st_mentor.id
    LEFT JOIN users u_mentor ON st_mentor.user_id = u_mentor.id
    LEFT JOIN staff st_hod ON s.hod_id = st_hod.id
    LEFT JOIN users u_hod ON st_hod.user_id = u_hod.id
    WHERE s.user_id = ?
    LIMIT 1
");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

if (!$profile) {
    jsonError('Student profile record not found.', 404);
}

$response = array_merge([
    'success' => true,
    'data'    => $profile,
    'profile' => $profile
], $profile);

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
