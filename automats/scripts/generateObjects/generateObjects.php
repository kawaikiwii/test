<?php
// script de sauvegarde des objets

require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
//require_once (dirname(__FILE__).'/../../inc/bizEn.php');
//require_once (dirname(__FILE__).'/../../inc/bizFr.php');

// on laisse plus de temps d'execution
ini_set("max_execution_time","10800");
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

// on teste si des arguments sont passés et si c'est le cas, on lance la génération par vague d'objects
if (sizeof($argv) > 1) {
    //echo "publicationDate DESC LIMIT ".(($argv[2] * 5001)).",5000\n";
    //echo "publicationDate DESC LIMIT ".(($argv[2] * 101)).",100\n";
    $obj = new $argv[1];
    if ($obj)
    {
        $obj->generateAll("workflowstate='published'", "publicationDate DESC LIMIT 200");
        //$obj->generateAll("mustgenerate=1 AND workflowstate='published'", "publicationDate ASC LIMIT ".(($argv[2] * 101)).",100");
    }
}

/*if (sizeof($argv) > 1) {
	for ($i=1; $i<sizeof($argv); $i++)
	{
		$obj = new $argv[$i];
		if ($obj)
		{
			$obj->generateAll(null, "publicationDate DESC LIMIT 0, 10000");
		}
	}
}*/

$temps_fin = microtime(true);
echo 'Temps d\'execution : '.round($temps_fin - $temps_debut."\n", 4);

