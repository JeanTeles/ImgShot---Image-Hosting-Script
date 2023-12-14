<?php
require_once('config.php');
$dbconnect = new db();
$dbconnect->connect();
session_start();

$dirname = uniqid();
mkdir("cache/zip/$dirname", 0777);
mkdir("cache/zip/$dirname/extracted", 0777);
$zip = new ZIP();
$zip->extract_upload('cache/zip/Sample-Pictures.zip', 'cache/zip/'.$dirname.'/extracted/');

if ($handle = opendir("cache/zip/$dirname/extracted")) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $upp = new upload();
            $upp->zipUpload("cache/zip/$dirname/extracted/$file");
        }
    }
    closedir($handle);
}
$zip->delTree("cache/zip/$dirname/extracted/");
$zip->delTree("cache/zip/$dirname/");
?>