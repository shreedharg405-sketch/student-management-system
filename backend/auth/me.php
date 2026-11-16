<?php
/**
 * Current User Profile & Session Check
 * GET /backend/auth/me.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireLogin();

$stmt = $pdo->prepare("SELECT id, name, email, role, is_active, created_at FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$userData = $stmt->fetch();

if (!$userData) {
    jsonError('User not found.', 404);
}

$details = [];
if ($userData['role'] === 'STUDENT') {
    $stStmt = $pdo->prepare("
        SELECT s.*, d.name AS department_name, d.code AS department_code, b.batch_name,
               u_mentor.name AS mentor_name, u_hod.name AS hod_name
        FROM students s
        LEFT JOIN departments d ON s.department_id = d.id
        LEFT JOIN batches b ON s.batch_id = b.id
        LEFT JOIN staff st_mentor ON s.mentor_id = st_mentor.id
        LEFT JOIN users u_mentor ON st_mentor.user_id = u_mentor.id
        LEFT JOIN staff st_hod ON s.hod_id = st_hod.id
        LEFT JOIN users u_hod ON st_hod.user_id = u_hod.id
        WHERE s.user_id = ?
        LIMIT 1
    ");
    $stStmt->execute([$user['id']]);
    $details = $stStmt->fetch() ?: [];
} else if ($userData['role'] === 'ASSISTANT_PROFESSOR' || $userData['role'] === 'HOD') {
    $sfStmt = $pdo->prepare("
        SELECT st.*, d.name AS department_name, d.code AS department_code
        FROM staff st
        LEFT JOIN departments d ON st.department_id = d.id
        WHERE st.user_id = ?
        LIMIT 1
    ");
    $sfStmt->execute([$user['id']]);
    $details = $sfStmt->fetch() ?: [];
}

jsonSuccess([
    'user'    => $userData,
    'profile' => $details
]);
