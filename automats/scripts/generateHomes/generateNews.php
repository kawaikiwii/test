<?php 
/***********************************************************
 
 PHP script executed by CRONTAB every 10 minutes
 @path /var/www/CRONTAB/scripts/updateObjects.php
 
 ***********************************************************/
 
//Initialize summer time
$summer_time = date("I");
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
require_once (dirname(__FILE__).'/../../../frontoffice/api/wcm.siteSearcher.php');

$session->startSession(wcmMembership::ROOT_USER_ID);

echo "\n####################################################\n";
echo "##\n";
echo "## 	GENERATE HOMES NEWS \n";
echo "##\n";
echo "####################################################\n";
echo "\n";
echo " Début : ".date("d-m-Y H:i:s")."\n";
echo "\n";

$bRequest = array("query"=>array(), "params"=>array());

$bRequest["params"]["ctId"] = "refreshHomesNews";
$bRequest["params"]["start"] = 0;
$bRequest["params"]["limit"] = 30;
$bRequest["params"]["sort"] = "publicationDate";
$bRequest["params"]["dir"] = "DESC";

$bRequest["query"]["fulltext"] = null;
$bRequest["query"]["classname"] = "news,notice";
$bRequest["query"]["folderid"] = null;

$date = date('Y-m-d H:i:s');
if($summer_time == 1)
	$newtime = strtotime($date.' + 2 hours');
else
	$newtime = strtotime($date.' + 1 hours');
$today = date('Y-m-d\TH:i:s', $newtime);
$newtime = strtotime($date.' - 2 month');
$yesterday = date('Y-m-d\TH:i:s', $newtime);

$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
echo "[$yesterday to $today]";
$sites = $session->getSite()->getArrayCodesSites();
foreach ($sites as $siteCode=>$siteId) {
    $site = new site();
    $site->refreshByCode($siteCode);
    $session->setSiteId($site->id);
    $session->setLanguage($site->language);
    
    $rootChannels = bizobject::getBizobjects("channel", "siteId='".$site->id."' AND parentId IS NULL", null);
    
    echo "Traitement du site : $site->title ($site->code / $site->id)\n";
    foreach ($rootChannels as $rootChannel) {
    
        echo "\tTraitement du channel : $rootChannel->title ($rootChannel->css)\n";
        
        // Aurait besoin d'une fonction qui renvoie tous les channels 'finaux' d'un parentChannel
        $channels = bizobject::getBizobjects("channel", "siteId='".$site->id."' AND parentId=$rootChannel->id  AND workflowstate='published'", null);
        
        $aChannels = array();
        $aResults = array();
		
		$bChannels = array();
		
        foreach ($channels as $channel) {
            $aChannels[] = $channel->id;
            
			$bChannels["$channel->id"][] = $channel->id;
            $channels2 = bizobject::getBizobjects("channel", "siteId='".$site->id."' AND parentId=$channel->id  AND workflowstate='published'", null);
            foreach ($channels2 as $channel2) {
                $aChannels[] = $channel2->id;
				$bChannels["$channel->id"][] = $channel2->id;
            }
            
        }
        
        $bRequest["query"]["channelid"] = $rootChannel->id.",".implode(",", $aChannels);
        $siteSearch = new wcmSiteSearcher($bRequest);
        
        $siteSearch->execute();
        
        $items = $siteSearch->getResult(0, 30);
        
        foreach ($items as $item) {
            $aResults[] = $item;
			echo "\t\t $item->id \t: $item->title\n";

        }
        
        $parameters = array("site"=>$site, "rootChannel"=>$rootChannel, "channels"=>$channels, "results"=>$aResults, "bChannels"=>$bChannels);
        
        $generator = new wcmTemplateGenerator(null, false, wcmWidget::VIEW_CONTENT);
        $generator->executeTemplate('PUBLISH/AFPRELAXNEWS/homes/news.tpl', $parameters);
    }
    /*
    $bRequest["query"]["channelid"] = null;
    $siteSearch = new wcmSiteSearcher($bRequest);
    
    $siteSearch->execute();
    
    $items = $siteSearch->getResult(0, 25);
    
    foreach ($items as $item) {
        $aResults[] = $item;
    }
    
    $parameters = array("site"=>$site, "results"=>$aResults);
    
    $generator = new wcmTemplateGenerator(null, false, wcmWidget::VIEW_CONTENT);
    $generator->executeTemplate('PUBLISH/AFPRELAXNEWS/homes/news.mobile.tpl', $parameters);
    */
}

unset($generator);
unset($siteSearch);
?>
