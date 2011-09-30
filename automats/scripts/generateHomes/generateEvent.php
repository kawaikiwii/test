<?php 
/***********************************************************

 PHP script executed by CRONTAB every 10 minutes
 @path /var/www/CRONTAB/scripts/updateObjects.php
 
 ***********************************************************/
 
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
require_once (dirname(__FILE__).'/../../../frontoffice/api/wcm.siteSearcher.php');

echo "\n####################################################\n";
echo "##\n";
echo "## 	GENERATE HOMES EVENT \n";
echo "##\n";
echo "####################################################\n";
echo "\n";
echo " Début : ".date("d-m-Y H:i:s")."\n";
echo "\n";

$session->startSession(wcmMembership::ROOT_USER_ID);

$sites = $session->getSite()->getArrayCodesSites();

foreach ($sites as $siteCode=>$siteId) {
    $site = new site();
    $site->refreshByCode($siteCode);
    $session->setSiteId($site->id);
    $session->setLanguage($site->language);
    
    $event = new event();
    $lastEventsSelected = $event->lastEventsSelected(2, $site->id);
    $mustSee = $event->mustSeeEvents(2, $site->id);
    
    $parameters = array("site"=>$site, "OURSEL"=>$lastEventsSelected, "MUSTSE"=>$mustSee);
        
	echo "Traitement du site : $site->title ($site->code / $site->id)\n";
    
    $generator = new wcmTemplateGenerator(null, false, wcmWidget::VIEW_CONTENT);
    $generator->executeTemplate('PUBLISH/AFPRELAXNEWS/homes/event.tpl', $parameters);
}

unset($generator);
unset($siteSearch);
?>
