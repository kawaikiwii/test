<?php
ini_set('error_reporting', E_ERROR);
ini_set('max_execution_time', 240);
ini_set("memory_limit","460M");

$temps_debut = microtime(true);

require_once dirname(__FILE__).'/../../initWebApp.php';
$import = new wcmImportAfpVideos(array());
$import->process();

$temps_fin = microtime(true);
echo 'Temps d\'execution : '.round($temps_fin - $temps_debut . "         \n\n\r", 4);
?>