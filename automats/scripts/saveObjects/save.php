<?php
// script de sauvegarde des objets

require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

// on laisse plus de temps d'execution
ini_set("max_execution_time", "10800");
ini_set("memory_limit", "460M");
ini_set('gd.jpeg_ignore_warning', 1);
// on enlève les messages d'erreur facultatifs
ini_set('error_reporting', E_ERROR);

$temps_debut = microtime(true);

echo "####################################################\n";
echo "##\n";
echo "## 	SAVE OBJECTS \n";
echo "##\n";
echo "####################################################\n";
echo "\n";
echo " Début : ".date("d-m-Y H:i:s")."\n";
echo "\n";

// on teste si des arguments sont passés et si c'est le cas, on lance la sauvegarde par vague d'objects
if (sizeof($argv) > 1) {
    echo "publicationDate DESC LIMIT ".(($argv[2] * 201)).",200\n";

    $obj = new $argv[1];
    if ($obj)
    {
        $obj->saveAll("mustGenerate=1", "id DESC LIMIT ".(($argv[2] * 201)).",200");
        //$obj->saveAll("siteid=6", "publicationDate DESC LIMIT ".(($argv[2] * 201)).",200");
        //$obj->saveAll("siteid=6", "publicationDate DESC LIMIT 0,50");
        //$obj->saveAll("mustGenerate=1", "id DESC LIMIT 0,100");
    }
}

$temps_fin = microtime(true);
echo 'Temps d\'execution : '.round($temps_fin - $temps_debut."\n", 4);

