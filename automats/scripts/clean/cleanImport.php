<?php 
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$db = new wcmDatabase($config['wcm.businessDB.connectionString']);
$USERNAME = 'admin';
$PASSWORD = 'kzq!2007';

//define("_BIZ_INVALID_XML", "XML not valid");

echo "####################################################\n";
echo "##\n";
echo "## 	CLEAN DES IMPORTS AFP/RELAXFIL\n";
echo "##\n";
echo "####################################################\n";
echo "\n";
echo " Début : ".date("d-m-Y H:i:s")."\n";
echo "\n";

if (!$session->login($USERNAME, $PASSWORD)) {
    echo "\n";
    echo "Connexion impossible\n";
    echo "\n";
    echo " Fin : ".date("d-m-Y H:i:s")."\n";
    echo "\n";
    exit();
}


//echo de la date
$query = "SELECT DATE(NOW())-5 as date_ancienne";
$rs = $db->executeQuery($query);
$rs->first();
while ($record = $rs->getRow()) {
	echo "date qui est appliquee : ".$record['date_ancienne']."\n"; 
	$continue = @$rs->next();
}

$query = "SELECT DATE(NOW()-INTERVAL 5 DAY) as date_ancienne";
$rs = $db->executeQuery($query);
$rs->first();
while ($record = $rs->getRow()) {
        echo "la mienne : ".$record['date_ancienne']."\n";
	$continue = @$rs->next();
}




//test avec ma date
/*$query = "SELECT id, title FROM `biz_news` WHERE workflowState = 'draft_import' AND DATE(createdAt) < DATE(NOW())-5 ORDER BY createdAt ASC LIMIT 200";
$nbDeleted = 0;
$nbError = 0;

$rs = $db->executeQuery($query);
$rs->first();

while ($record = $rs->getRow()) {
    $newsToDelete = new news(null, $record['id']);
    echo $newsToDelete->id." : ".$newsToDelete->title." : ";
    $continue = @$rs->next();

}
echo "\n";
echo "Imports supprimés : ".$nbDeleted." | Erreur : ".$nbError."\n";
echo "\n";
echo " Fin : ".date("d-m-Y H:i:s")."\n";
echo "\n";
*/


//vrai suppression !!!
$query = "SELECT id, title FROM `biz_news` WHERE workflowState = 'draft_import' AND DATE(createdAt) < DATE(NOW()-INTERVAL 5 DAY) ORDER BY createdAt ASC LIMIT 200";
$nbDeleted = 0;
$nbError = 0;

$rs = $db->executeQuery($query);
$rs->first();

while ($record = $rs->getRow()) {
    $newsToDelete = new news(null, $record['id']);
    echo $newsToDelete->id." : ".$newsToDelete->title." : ";
    if ($newsToDelete->delete()) {
        echo "deleted\n";
        $nbDeleted++;
    } else {
        echo "ERREUR\n";
        $nbError++;
    }
    
    if (($nbDeleted + $nbError) % 10 == 0) {
        echo "------- PAUSE : ".($nbDeleted + $nbError)." -------\n";
        sleep(2);
    }
    
    $continue = @$rs->next();
    
}
echo "\n";
echo "Imports supprimés : ".$nbDeleted." | Erreur : ".$nbError."\n";
echo "\n";
echo " Fin : ".date("d-m-Y H:i:s")."\n";
echo "\n";
$session->logout();
?>
