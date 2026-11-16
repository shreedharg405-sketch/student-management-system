<?php
/**
 * HOD Exams Dispatcher API
 * Routes GET, POST, PUT, DELETE for Exam schedules
 */

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    require __DIR__ . '/exam-create.php';
} else if ($method === 'PUT') {
    require __DIR__ . '/exam-update.php';
} else if ($method === 'DELETE') {
    require __DIR__ . '/exam-delete.php';
} else {
    // GET: return department exams
    require __DIR__ . '/../staff/exams.php';
}

