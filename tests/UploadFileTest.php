<?php
require '../vendor/autoload.php';

use UploadFile\UploadFile;

$uploadFile = new UploadFile();
$fileInfo = [
    "name" => "ps-logo.jpg",
    "type" => "image/jpeg",
    "tmp_name" => "/home/www/tmp/phpMBu4TE",
    "error" => 0,
    "size" => 24722
];
$file = $_FILES['name'];
try {
    $dir = __DIR__ . "/images";
    $res = $uploadFile->dir($dir)
        ->sub(true)
        ->size(309601)
        ->type(['image/jpg', 'image/jpeg', 'image/png'])
        ->upload($file);
    var_dump($res);

} catch (Throwable $e) {
    var_dump($e->getMessage());
}

