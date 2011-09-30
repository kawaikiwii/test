<?php
ini_set('error_reporting', E_ERROR);

$temps_debut = microtime(true);

require_once dirname(__FILE__).'/initWebApp.php';
$import = new wcmMigration(array('siteId'=>$argv[1], 'step'=>$argv[2], 'newsFolder'=>$argv[3]));
$import->process();

$temps_fin = microtime(true);
echo 'Temps d\'execution : '.round($temps_fin - $temps_debut . "\n", 4);
?>