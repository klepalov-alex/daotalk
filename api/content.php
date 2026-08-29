<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_admin();

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $rows = $pdo->query('SELECT lang, content_key, value FROM content')->fetchAll();
    $out=['en'=>[],'ru'=>[]];
    foreach($rows as $row){ $lang=$row['lang']==='ru'?'ru':'en'; $out[$lang][$row['content_key']]=$row['value']; }
    json_response(['ok'=>true,'strings'=>$out]);
}
if ($method === 'PUT' || $method === 'POST') {
    $data=request_json();
    foreach(['en','ru'] as $lang){
        $items = $data['strings'][$lang] ?? [];
        if (!is_array($items)) continue;
        foreach($items as $key=>$value){
            $stmt=$pdo->prepare('INSERT INTO content (lang,content_key,value) VALUES (?,?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)');
            $stmt->execute([$lang,(string)$key,(string)$value]);
        }
    }
    json_response(['ok'=>true]);
}
json_response(['ok'=>false,'error'=>'Method not allowed'],405);
