<?php

// script de sauvegarde des objets

require_once (dirname( __FILE__ ).'/../../inc/wcmInit.php');

// on laisse plus de temps d'execution
ini_set("max_execution_time", "10800");
ini_set("memory_limit","460M");
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
if (sizeof($argv) > 1)
{
	for ($i=1; $i<sizeof($argv); $i++)
	{
		$obj = new $argv[$i];
		if ($obj) $obj->initAllManagerPathId();
	}
}

$temps_fin = microtime(true);
echo 'Temps d\'execution : '.round($temps_fin - $temps_debut . "\n", 4);

