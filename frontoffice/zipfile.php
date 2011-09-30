<?php
// Initialize WCM API
require_once (dirname( __FILE__ ).'/inc/wcmInit.php');

$files = ( isset ($_POST['pics']))?$_POST['pics']:NULL;
if ($files != NULL)
{
    $zip = new ZipArchive();
    $filename = "/tmp/wcm/".$CURRENT_USER->id."-".date("Ymd-His")."-afprelaxnews-picturespackage.zip";

    if ($zip->open($filename, ZIPARCHIVE::CREATE+ZIPARCHIVE::OVERWRITE) !== TRUE)
    {
        exit ("Impossible d'ouvrir <$filename>\n");
    }

    foreach ($files as $file)
    {
        if (file($file))
        {
            $zip->addFile($file, basename($file));
        }
    }
    $zip->close();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=".basename($filename).";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filename));
    readfile("$filename");

    unlink($filename);
}