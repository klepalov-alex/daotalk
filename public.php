<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$pdo = db();
$contentRows = $pdo->query('SELECT lang, content_key, value FROM content ORDER BY id')->fetchAll();
$strings = ['en' => [], 'ru' => []];
foreach ($contentRows as $row) {
    $lang = $row['lang'] === 'ru' ? 'ru' : 'en';
    $strings[$lang][$row['content_key']] = $row['value'];
}

$teachers = $pdo->query('SELECT id, avatar, name, native_speaker AS native, origin, hsk, teaches, edu_en, edu_ru, exp_en, exp_ru, sort_order FROM teachers ORDER BY sort_order ASC, id ASC')->fetchAll();
foreach ($teachers as &$teacher) $teacher['native'] = (bool)$teacher['native'];
unset($teacher);

$images = [];
$uploadDir = dirname(__DIR__) . '/uploads/culture';
$publicPrefix = 'uploads/culture/';
for ($i = 0; $i < 4; $i++) {
    $matches = glob($uploadDir . '/slide' . $i . '.*') ?: [];
    if ($matches) {
        $file = basename($matches[0]);
        $images['slide' . $i] = $publicPrefix . $file;
    }
}

json_response([
    'ok' => true,
    'strings' => $strings,
    'teachers' => $teachers,
    'images' => $images,
]);
