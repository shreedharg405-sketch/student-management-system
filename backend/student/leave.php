<?php
/**
 * Student Leave Dispatcher API
 * Routes POST to leave-create.php and GET to leaves.php
 */

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    require __DIR__ . '/leave-create.php';
} else {
    require __DIR__ . '/leaves.php';
}

