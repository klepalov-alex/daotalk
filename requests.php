<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $d = request_json();
    $stmt=$pdo->prepare('INSERT INTO requests (name,email,phone,contact,level,format,goal,comment) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([
        trim((string)($d['name']??'')), trim((string)($d['email']??'')), trim((string)($d['phone']??'')), trim((string)($d['contact']??'')),
        trim((string)($d['level']??'')), trim((string)($d['format']??'')), trim((string)($d['goal']??'')), trim((string)($d['comment']??''))
    ]);
    json_response(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
}

require_admin();
if ($method === 'GET') {
    $rows=$pdo->query('SELECT id,created_at,name,email,phone,contact,level,format,goal,comment,status FROM requests ORDER BY created_at DESC')->fetchAll();
    json_response(['ok'=>true,'requests'=>$rows]);
}
if ($method === 'PATCH') {
    $d=request_json(); $id=(int)($d['id']??0); $status=(string)($d['status']??'new');
    $allowed=['new','contacted','trial','paid','lost'];
    if($id<1 || !in_array($status,$allowed,true)) json_response(['ok'=>false,'error'=>'Invalid request'],400);
    $stmt=$pdo->prepare('UPDATE requests SET status=? WHERE id=?'); $stmt->execute([$status,$id]); json_response(['ok'=>true]);
}
json_response(['ok'=>false,'error'=>'Method not allowed'],405);
