<?php
/**
 * HOD Timetable Dispatcher API
 * Routes GET, POST, PUT, DELETE for Timetable slots
 */

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    require __DIR__ . '/timetable-create.php';
} else if ($method === 'PUT') {
    require __DIR__ . '/timetable-update.php';
} else if ($method === 'DELETE') {
    require __DIR__ . '/timetable-delete.php';
} else {
    // GET: return department timetable
    $_GET['all'] = 'true';
    require __DIR__ . '/../staff/timetable.php';
}

