<?php
require '../config/path.php';
if( isset($_GET['filename']) ){
    $type = mime_content_type(__DIR__.'/../'.PATH_CVSQL.$_GET['filename']);
    header("Content-disposition: attachment; filename=$_GET[filename]");
    header("Content-Type: application/force-download");
    header("Content-Transfer-Encoding: $type\n"); // Surtout ne pas enlever le \n
    header("Content-Length: ".filesize(__DIR__.'/../'.PATH_CVSQL.$_GET['filename']));
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
    header("Expires: 0");
    readfile(__DIR__.'/../'.PATH_CVSQL.$_GET['filename']);
}
