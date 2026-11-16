<?php
/**
 * User Login Endpoint
 * POST /backend/auth/login.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed. Only POST is accepted.', 405);
}

$input = getRequestData();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonError('Please provide both email and password.', 400);
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    jsonError('Invalid email or password.', 401);
}

if (!$user['is_active']) {
    jsonError('Account is inactive. Please contact administration.', 403);
}

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['name']    = $user['name'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

// Cache contextual entity identifiers
if ($user['role'] === 'STUDENT') {
    $stStmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ? LIMIT 1");
    $stStmt->execute([$user['id']]);
    $student = $stStmt->fetch();
    if ($student) {
        $_SESSION['student_id']    = (int)$student['id'];
        $_SESSION['department_id'] = (int)$student['department_id'];
        $_SESSION['batch_id']      = (int)$student['batch_id'];
    }
} else if ($user['role'] === 'ASSISTANT_PROFESSOR' || $user['role'] === 'HOD') {
    $sfStmt = $pdo->prepare("SELECT * FROM staff WHERE user_id = ? LIMIT 1");
    $sfStmt->execute([$user['id']]);
    $staff = $sfStmt->fetch();
    if ($staff) {
        $_SESSION['staff_id']      = (int)$staff['id'];
        $_SESSION['department_id'] = (int)$staff['department_id'];
    }
}

// Return formatted user object
echo json_encode([
    'success' => true,
    'user'    => [
        'id'    => (int)$user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role']
    ]
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
