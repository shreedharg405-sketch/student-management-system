<?php
/**
 * Staff Attendance Submission & Inquiry API
 * POST / GET /backend/staff/attendance.php
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
$facultyId = (int)$staff['id'];
$isHOD = ($user['role'] === 'HOD');

// Handle GET: Fetch existing attendance for a class slot
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $date      = $_GET['date'] ?? date('Y-m-d');
    $period    = isset($_GET['period']) ? (int)$_GET['period'] : 0;
    $subjectId = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
    $batchId   = !empty($_GET['batch_id']) ? (int)$_GET['batch_id'] : null;

    $stmt = $pdo->prepare("
        SELECT a.id, a.date, a.period, a.subject_id, a.batch_id, a.faculty_id,
               sub.subject_code, sub.subject_name, u_fac.name AS faculty_name
        FROM attendance a
        JOIN subjects sub ON a.subject_id = sub.id
        JOIN staff st ON a.faculty_id = st.id
        JOIN users u_fac ON st.user_id = u_fac.id
        WHERE a.date = ? AND a.period = ? AND a.subject_id = ?
          AND (a.batch_id = ? OR (a.batch_id IS NULL AND ? IS NULL))
        LIMIT 1
    ");
    $stmt->execute([$date, $period, $subjectId, $batchId, $batchId]);
    $header = $stmt->fetch();

    if (!$header) {
        jsonSuccess(['marked' => false, 'header' => null, 'records' => []]);
    }

    $recStmt = $pdo->prepare("
        SELECT ar.id, ar.student_id, ar.status, ar.remarks,
               s.register_number, u.name AS student_name
        FROM attendance_records ar
        JOIN students s ON ar.student_id = s.id
        JOIN users u ON s.user_id = u.id
        WHERE ar.attendance_id = ?
        ORDER BY s.register_number ASC
    ");
    $recStmt->execute([$header['id']]);
    $records = $recStmt->fetchAll();

    jsonSuccess([
        'marked'  => true,
        'header'  => $header,
        'records' => $records
    ]);
}

// Handle POST & PUT: Submit or update attendance with authorization
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = getRequestData();

    $date      = trim($input['date'] ?? date('Y-m-d'));
    $period    = (int)($input['period'] ?? 0);
    $subjectId = (int)($input['subject_id'] ?? 0);
    $batchId   = !empty($input['batch_id']) ? (int)$input['batch_id'] : null;
    $records   = $input['records'] ?? [];
    $inputAttId = (int)($input['attendance_id'] ?? $_GET['attendance_id'] ?? 0);

    if (empty($date) || $period <= 0 || $subjectId <= 0 || empty($records)) {
        jsonError('Missing required attendance data (date, period, subject_id, records).', 400);
    }

    // 1. Authorization: Verify faculty is assigned to this subject/timetable slot or is HOD
    if (!$isHOD) {
        $authStmt = $pdo->prepare("
            SELECT COUNT(*) AS cnt 
            FROM timetable 
            WHERE faculty_id = ? AND subject_id = ? 
              AND (batch_id = ? OR batch_id IS NULL)
        ");
        $authStmt->execute([$facultyId, $subjectId, $batchId]);
        $row = $authStmt->fetch();
        if ((int)$row['cnt'] === 0) {
            jsonError('Forbidden: You are not assigned to take attendance for this subject and batch.', 403);
        }
    }

    // 2. Check existing attendance by slot or by attendance_id
    $dupStmt = $pdo->prepare("
        SELECT id FROM attendance 
        WHERE date = ? AND period = ? AND subject_id = ? 
          AND (batch_id = ? OR (batch_id IS NULL AND ? IS NULL))
        LIMIT 1
    ");
    $dupStmt->execute([$date, $period, $subjectId, $batchId, $batchId]);
    $existing = $dupStmt->fetch();
    $existingId = $existing ? (int)$existing['id'] : 0;
    $attendanceId = $inputAttId > 0 ? $inputAttId : $existingId;

    // 3. Save or Update Attendance Header & Records within a transaction
    try {
        $pdo->beginTransaction();

        if ($attendanceId > 0) {
            // Update existing attendance header
            $upHeader = $pdo->prepare("UPDATE attendance SET faculty_id = ? WHERE id = ?");
            $upHeader->execute([$facultyId, $attendanceId]);

            // Clear old records for replacement
            $delRec = $pdo->prepare("DELETE FROM attendance_records WHERE attendance_id = ?");
            $delRec->execute([$attendanceId]);
        } else {
            // Insert new attendance header
            $insHeader = $pdo->prepare("
                INSERT INTO attendance (date, period, subject_id, batch_id, faculty_id)
                VALUES (?, ?, ?, ?, ?)
            ");
            $insHeader->execute([$date, $period, $subjectId, $batchId, $facultyId]);
            $attendanceId = (int)$pdo->lastInsertId();
        }

        $insRecord = $pdo->prepare("
            INSERT INTO attendance_records (attendance_id, student_id, status, remarks)
            VALUES (?, ?, ?, ?)
        ");

        $presentCount = 0;
        $absentCount = 0;

        foreach ($records as $rec) {
            $stId = (int)($rec['student_id'] ?? 0);
            $status = ($rec['status'] === 'Absent') ? 'Absent' : 'Present';
            $remarks = trim($rec['remarks'] ?? '');

            if ($stId > 0) {
                $insRecord->execute([$attendanceId, $stId, $status, $remarks]);
                if ($status === 'Present') {
                    $presentCount++;
                } else {
                    $absentCount++;
                }
            }
        }

        $pdo->commit();

        jsonSuccess([
            'attendance_id' => $attendanceId,
            'date'          => $date,
            'period'        => $period,
            'present_count' => $presentCount,
            'absent_count'  => $absentCount,
            'total_marked'  => $presentCount + $absentCount
        ], 200, 'Attendance successfully recorded.');

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonError('Database transaction failed while saving attendance: ' . $e->getMessage(), 500);
    }
}
