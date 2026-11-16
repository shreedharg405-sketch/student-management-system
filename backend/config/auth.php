<?php
/**
 * Authentication Helper & Session RBAC Security Module
 * Student Management System (SMS)
 */

// Configure session cookies
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    // 24 hours session lifetime
    ini_set('session.gc_maxlifetime', 86400);
    session_set_cookie_params([
        'lifetime' => 86400,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Set CORS & Content-Type Headers
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Standard JSON Success Response
 */
function jsonSuccess($data = [], $statusCode = 200, $message = null) {
    http_response_code($statusCode);
    $response = [
        'success' => true,
        'data'    => $data
    ];
    if ($message !== null) {
        $response['message'] = $message;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Standard JSON Error Response
 */
function jsonError($message = 'An error occurred', $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Safely parse incoming request payload (handles JSON body and POST fields)
 */
function getRequestData() {
    $data = [];
    $rawInput = file_get_contents('php://input');
    if (!empty($rawInput)) {
        $json = json_decode($rawInput, true);
        if (is_array($json)) {
            $data = $json;
        }
    }
    if (!empty($_POST)) {
        $data = array_merge($data, $_POST);
    }
    return $data;
}

/**
 * Require Logged In User (Any Role)
 * Returns active user details or returns 401 Unauthorized
 */
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        jsonError('Unauthorized: Please log in to continue.', 401);
    }
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'role'  => $_SESSION['role'] ?? ''
    ];
}

/**
 * Require Student Role
 * Enforces STUDENT role; returns 403 Forbidden otherwise
 */
function requireStudent() {
    $user = requireLogin();
    if ($user['role'] !== 'STUDENT') {
        jsonError('Forbidden: Access restricted to Students only.', 403);
    }
    return $user;
}

/**
 * Require Staff Role (Assistant Professor or HOD)
 * Enforces ASSISTANT_PROFESSOR or HOD role; returns 403 Forbidden otherwise
 */
function requireStaff() {
    $user = requireLogin();
    if ($user['role'] !== 'ASSISTANT_PROFESSOR' && $user['role'] !== 'HOD') {
        jsonError('Forbidden: Access restricted to Faculty members.', 403);
    }
    return $user;
}

/**
 * Require HOD Role
 * Enforces strict HOD role; returns 403 Forbidden otherwise
 */
function requireHOD() {
    $user = requireLogin();
    if ($user['role'] !== 'HOD') {
        jsonError('Forbidden: Access restricted to Head of Department (HOD) only.', 403);
    }
    return $user;
}
