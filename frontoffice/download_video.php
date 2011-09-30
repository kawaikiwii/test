<?php
$file = urldecode($_GET['file']);
//$file = str_replace("|","/",$file);
$headers = get_headers($file, 1);
//die($headers["Content-Length"]);
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$file);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: '.$headers["Content-Length"]);
ob_clean();
flush();
readfile($file);
?>