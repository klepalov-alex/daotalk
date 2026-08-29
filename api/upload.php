<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['ok'=>false,'error'=>'Method not allowed'],405);
$idx=(int)($_POST['index']??-1);
if($idx<0 || $idx>3) json_response(['ok'=>false,'error'=>'Invalid slide'],400);
if(empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) json_response(['ok'=>false,'error'=>'Upload failed'],400);
$file=$_FILES['image'];
if($file['size'] > 5*1024*1024) json_response(['ok'=>false,'error'=>'Image is larger than 5 MB'],400);
$mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
if(!isset($allowed[$mime])) json_response(['ok'=>false,'error'=>'Only JPG, PNG or WebP images are allowed'],400);
$dir=dirname(__DIR__).'/uploads/culture';
if(!is_dir($dir) && !mkdir($dir,0755,true)) json_response(['ok'=>false,'error'=>'Cannot create upload directory'],500);
foreach(glob($dir.'/slide'.$idx.'.*') ?: [] as $old){ @unlink($old); }
$filename='slide'.$idx.'.'.$allowed[$mime];
if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$filename)) json_response(['ok'=>false,'error'=>'Cannot save image'],500);
json_response(['ok'=>true,'url'=>'uploads/culture/'.$filename]);
