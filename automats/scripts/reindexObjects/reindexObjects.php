<?php 
// script de r�indexation des objets

require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
// on laisse plus de temps d'execution
ini_set("max_execution_time", "18000");
// on enl�ve les messages d'erreur facultatifs
ini_set('error_reporting', E_ERROR);

$temps_debut = microtime(true);
$project = wcmProject::getInstance();
$config = wcmConfig::getInstance();
// on teste si des arguments sont pass�s et si c'est le cas, on lance la sauvegarde par vague d'objects


if (sizeof($argv) > 1)
{
	$bizsearch = wcmBizsearch::getInstance();
	
	for ($i=1; $i<sizeof($argv); $i++)
	{
		echo "-----------------------------------\n";
		echo "Desindexation de : " . $argv[$i] . "\n";
		$bizsearch->deindexBizobjects($argv[$i]);
		echo "Reindexation de " . $argv[$i] . "\n\n";
		$bizsearch->reindexBizobjects($argv[$i]);//, NULL, "publicationDate DESC");
		
	}
}
