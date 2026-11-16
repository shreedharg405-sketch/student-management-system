<?php
/**
 * Student Apply Leave Request API
 * POST /backend/student/leave-create.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$user = requireStudent();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed. Only POST is accepted.', 405);
}

$stStmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ? LIMIT 1");
$stStmt->execute([$user['id']]);
$student = $stStmt->fetch();

if (!$student) {
    jsonError('Student record not found.', 404);
}
$studentId = (int)$student['id'];

// Can receive via multipart/form-data or JSON
$input = getRequestData();
$leaveType = trim($input['leave_type'] ?? '');
$fromDate  = trim($input['from_date'] ?? '');
$toDate    = trim($input['to_date'] ?? '');
$reason    = trim($input['reason'] ?? '');

if (empty($leaveType) || empty($fromDate) || empty($toDate) || empty($reason)) {
    jsonError('All fields (Leave Type, From Date, To Date, Reason) are required.', 400);
}

if ($fromDate > $toDate) {
    jsonError('From Date cannot be later than To Date.', 400);
}

// Handle file upload safely
$attachmentUrl = '';
if (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['attachment'];
    $maxSize = 5 * 1024 * 1024; // 5 MB

    if ($file['size'] > $maxSize) {
        jsonError('Attachment exceeds maximum limit of 5 MB.', 400);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx'];

    if (!in_array($ext, $allowedExts)) {
        jsonError('Invalid file type. Allowed formats: PDF, PNG, JPG, JPEG, DOC, DOCX.', 400);
    }

    $uploadDir = __DIR__ . '/../uploads/documents/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $safeName = 'doc_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetPath = $uploadDir . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        jsonError('Failed to save uploaded attachment.', 500);
    }

    $attachmentUrl = 'uploads/documents/' . $safeName;
}

$stmt = $pdo->prepare("
    INSERT INTO leave_requests (student_id, leave_type, from_date, to_date, reason, attachment_url, status)
    VALUES (?, ?, ?, ?, ?, ?, 'PENDING_MENTOR')
");
$stmt->execute([
    $studentId,
    $leaveType,
    $fromDate,
    $toDate,
    $reason,
    $attachmentUrl
]);

$newLeaveId = (int)$pdo->lastInsertId();

jsonSuccess([
    'id'             => $newLeaveId,
    'status'         => 'PENDING_MENTOR',
    'attachment_url' => $attachmentUrl
], 201, 'Leave application submitted successfully. Pending mentor review.');
