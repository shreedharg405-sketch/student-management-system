<?php
/**
 * Staff Leave Review & Approval API
 * GET / POST /backend/staff/leaves.php
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

// GET: Fetch leave requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($isHOD) {
        // HOD sees all department leaves
        $stmt = $pdo->prepare("
            SELECT 
                lr.id,
                lr.leave_type,
                lr.from_date,
                lr.to_date,
                DATEDIFF(lr.to_date, lr.from_date) + 1 AS duration_days,
                lr.reason,
                lr.attachment_url,
                lr.status,
                lr.created_at,
                s.id AS student_id,
                s.register_number,
                u.name AS student_name,
                b.batch_name,
                u_mentor.name AS mentor_name
            FROM leave_requests lr
            JOIN students s ON lr.student_id = s.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN batches b ON s.batch_id = b.id
            LEFT JOIN staff st_mentor ON s.mentor_id = st_mentor.id
            LEFT JOIN users u_mentor ON st_mentor.user_id = u_mentor.id
            WHERE s.department_id = ?
            ORDER BY 
                CASE lr.status 
                    WHEN 'PENDING_HOD' THEN 1 
                    WHEN 'PENDING_MENTOR' THEN 2 
                    ELSE 3 
                END,
                lr.created_at DESC
        ");
        $stmt->execute([$deptId]);
    } else {
        // Assistant Professor sees mentee leaves
        $stmt = $pdo->prepare("
            SELECT 
                lr.id,
                lr.leave_type,
                lr.from_date,
                lr.to_date,
                DATEDIFF(lr.to_date, lr.from_date) + 1 AS duration_days,
                lr.reason,
                lr.attachment_url,
                lr.status,
                lr.created_at,
                s.id AS student_id,
                s.register_number,
                u.name AS student_name,
                b.batch_name
            FROM leave_requests lr
            JOIN students s ON lr.student_id = s.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN batches b ON s.batch_id = b.id
            WHERE s.mentor_id = ?
            ORDER BY 
                CASE lr.status WHEN 'PENDING_MENTOR' THEN 1 ELSE 2 END,
                lr.created_at DESC
        ");
        $stmt->execute([$staffId]);
    }

    $leaves = $stmt->fetchAll();

    // Attach response history to each leave
    foreach ($leaves as &$lv) {
        $respStmt = $pdo->prepare("
            SELECT resp.stage, resp.action, resp.remarks, resp.created_at, u.name AS responder_name
            FROM leave_responses resp
            JOIN users u ON resp.responder_id = u.id
            WHERE resp.leave_request_id = ?
            ORDER BY resp.created_at ASC
        ");
        $respStmt->execute([$lv['id']]);
        $lv['responses'] = $respStmt->fetchAll();
    }
    unset($lv);

    echo json_encode([
        'success' => true,
        'data'    => $leaves,
        'leaves'  => $leaves
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// POST: Review and update leave status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getRequestData();
    $leaveId = (int)($input['leave_id'] ?? $input['id'] ?? $_GET['leave_id'] ?? $_GET['id'] ?? 0);
    $action  = strtoupper(trim($input['action'] ?? '')); // APPROVE or REJECT
    $remarks = trim($input['remarks'] ?? '');

    if ($leaveId <= 0 || !in_array($action, ['APPROVE', 'REJECT'])) {
        jsonError('Invalid input. Leave ID and action (APPROVE or REJECT) are required.', 400);
    }

    $lvStmt = $pdo->prepare("
        SELECT lr.*, s.mentor_id, s.department_id 
        FROM leave_requests lr
        JOIN students s ON lr.student_id = s.id
        WHERE lr.id = ? LIMIT 1
    ");
    $lvStmt->execute([$leaveId]);
    $leave = $lvStmt->fetch();

    if (!$leave) {
        jsonError('Leave request not found.', 404);
    }

    $newStatus = '';
    $stage = '';

    if ($isHOD) {
        // HOD review
        $stage = 'HOD';
        $newStatus = ($action === 'APPROVE') ? 'HOD_APPROVED' : 'HOD_REJECTED';
    } else {
        // Mentor review
        if ((int)$leave['mentor_id'] !== $staffId) {
            jsonError('Forbidden: You are not assigned as mentor for this student.', 403);
        }
        if ($leave['status'] !== 'PENDING_MENTOR') {
            jsonError('This leave request is not pending mentor review.', 400);
        }
        $stage = 'MENTOR';
        $newStatus = ($action === 'APPROVE') ? 'PENDING_HOD' : 'MENTOR_REJECTED';
    }

    try {
        $pdo->beginTransaction();

        // Update leave header status
        $upStmt = $pdo->prepare("UPDATE leave_requests SET status = ? WHERE id = ?");
        $upStmt->execute([$newStatus, $leaveId]);

        // Insert response record
        $insStmt = $pdo->prepare("
            INSERT INTO leave_responses (leave_request_id, responder_id, stage, action, remarks)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insStmt->execute([$leaveId, $user['id'], $stage, $action . 'D', $remarks]);

        $pdo->commit();

        jsonSuccess([
            'leave_id'   => $leaveId,
            'new_status' => $newStatus,
            'stage'      => $stage
        ], 200, "Leave request successfully marked as {$newStatus}.");

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonError('Failed to record review: ' . $e->getMessage(), 500);
    }
}
