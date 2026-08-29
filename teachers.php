<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_admin();

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $rows = $pdo->query('SELECT id, avatar, name, native_speaker AS native, origin, hsk, teaches, edu_en, edu_ru, exp_en, exp_ru, sort_order FROM teachers ORDER BY sort_order ASC, id ASC')->fetchAll();
    foreach ($rows as &$r) $r['native'] = (bool)$r['native'];
    unset($r);
    json_response(['ok' => true, 'teachers' => $rows]);
}

$data = request_json();
if ($method === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO teachers (avatar,name,native_speaker,origin,hsk,teaches,edu_en,edu_ru,exp_en,exp_ru,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([
        mb_substr((string)($data['avatar'] ?? ''), 0, 10),
        trim((string)($data['name'] ?? '')),
        !empty($data['native']) ? 1 : 0,
        trim((string)($data['origin'] ?? '')),
        trim((string)($data['hsk'] ?? '')),
        trim((string)($data['teaches'] ?? '')),
        trim((string)($data['edu_en'] ?? '')),
        trim((string)($data['edu_ru'] ?? '')),
        trim((string)($data['exp_en'] ?? '')),
        trim((string)($data['exp_ru'] ?? '')),
        (int)($data['sort_order'] ?? 0),
    ]);
    json_response(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
}

if ($method === 'PUT') {
    $id = (int)($data['id'] ?? 0);
    if ($id < 1) json_response(['ok'=>false,'error'=>'Invalid teacher id'],400);
    $stmt = $pdo->prepare('UPDATE teachers SET avatar=?, name=?, native_speaker=?, origin=?, hsk=?, teaches=?, edu_en=?, edu_ru=?, exp_en=?, exp_ru=?, sort_order=? WHERE id=?');
    $stmt->execute([
        mb_substr((string)($data['avatar'] ?? ''), 0, 10), trim((string)($data['name'] ?? '')), !empty($data['native']) ? 1 : 0,
        trim((string)($data['origin'] ?? '')), trim((string)($data['hsk'] ?? '')), trim((string)($data['teaches'] ?? '')),
        trim((string)($data['edu_en'] ?? '')), trim((string)($data['edu_ru'] ?? '')), trim((string)($data['exp_en'] ?? '')), trim((string)($data['exp_ru'] ?? '')),
        (int)($data['sort_order'] ?? 0), $id
    ]);
    json_response(['ok' => true]);
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id < 1) json_response(['ok'=>false,'error'=>'Invalid teacher id'],400);
    $stmt = $pdo->prepare('DELETE FROM teachers WHERE id=?');
    $stmt->execute([$id]);
    json_response(['ok' => true]);
}

json_response(['ok'=>false,'error'=>'Method not allowed'],405);
