<?php

$headers = apache_request_headers();
$filename = $_SERVER["SCRIPT_FILENAME"];
$extension = pathinfo($filename, PATHINFO_EXTENSION);
$fileModificationTime = filemtime($filename);

if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $fileModificationTime) && $extension !== 'php') {
    // Client's cache IS current, so we just respond '304 Not Modified'.
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModificationTime).' GMT', true, 304);
} else {
    // Image not cached or cache outdated, we respond '200 OK' and output the image.
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModificationTime).' GMT');
}