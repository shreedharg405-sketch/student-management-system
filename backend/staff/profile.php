<?php
/**
 * Staff Profile API
 * GET /backend/staff/profile.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStaff();

$stmt = $pdo->prepare("
    SELECT 
        st.id AS staff_id,
        st.id AS id,
        st.staff_code,
        st.designation,
        st.phone,
        st.qualification,
        st.joining_date,
        u.name AS staff_name,
        u.name AS name,
        u.email AS staff_email,
        u.email AS email,
        u.role,
        d.id AS department_id,
        d.name AS department_name,
        d.code AS department_code,
        d.code AS department
    FROM staff st
    JOIN users u ON st.user_id = u.id
    LEFT JOIN departments d ON st.department_id = d.id
    WHERE st.user_id = ?
    LIMIT 1
");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

if (!$profile) {
    jsonError('Staff profile not found.', 404);
}

$response = array_merge([
    'success' => true,
    'data'    => $profile,
    'profile' => $profile
], $profile);

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
