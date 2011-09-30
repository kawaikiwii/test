<?php
//require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
//require_once (dirname(__FILE__).'/../../../frontoffice/api/wcm.siteSearcher.php');

//$session->startSession(wcmMembership::ROOT_USER_ID);

echo "\n####################################################\n";
echo "##\n";
echo "##        GENERATE HOMES NEWS \n";
echo "##\n";
echo "####################################################\n";
echo "\n";
echo " Début : ".date("d-m-Y H:i:s")."\n";
echo "\n";


$date = date('Y-m-d H:i:s');
if(date("I") == 1){
	echo '+2';
        $newtime = strtotime($date.' + 2 hours');
}else{
	echo '+1';
        $newtime = strtotime($date.' + 1 hours');
}
$today = date('Y-m-d\TH:i:s', $newtime);
$newtime = strtotime($date.' - 2 month');
$yesterday = date('Y-m-d\TH:i:s', $newtime);
echo "[$yesterday to $today] \n";
echo $today;

?>
