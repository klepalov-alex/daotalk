<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_admin();
$idx=(int)($_POST['index']??-1);
if($idx<0 || $idx>3) json_response(['ok'=>false,'error'=>'Invalid slide'],400);
$dir=dirname(__DIR__).'/uploads/culture';
foreach(glob($dir.'/slide'.$idx.'.*') ?: [] as $old){ @unlink($old); }
json_response(['ok'=>true]);
