<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    json_response(['ok' => true, 'authenticated' => !empty($_SESSION['dt_admin'])]);
}

$data = request_json();
$password = (string)($data['password'] ?? '');
if (!password_verify($password, $config['admin_password_hash'])) {
    json_response(['ok' => false, 'error' => 'Incorrect password'], 403);
}

session_regenerate_id(true);
$_SESSION['dt_admin'] = true;
json_response(['ok' => true]);
