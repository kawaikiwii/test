<?php
/***********************************************************

 PHP script executed by CRONTAB every 10 minutes
 @path /var/www/CRONTAB/scripts/updateObjects.php

 ***********************************************************/

//Initialize summer time
$summer_time = date("I");
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
require_once (dirname(__FILE__).'/../../../frontoffice/api/wcm.siteSearcher.php');

echo "\n####################################################\n";
echo "##\n";
echo "## 	GENERATE HOMES VIDEO \n";
echo "##\n";
echo "####################################################\n";
echo "\n";
echo " Début : ".date("d-m-Y H:i:s")."\n";
echo "\n";

$session->startSession(wcmMembership::ROOT_USER_ID);

$bRequest = array("query"=>array(), "params"=>array());

$bRequest["params"]["ctId"] = "refreshHomesVideo";
$bRequest["params"]["start"] = 0;
$bRequest["params"]["limit"] = 25;
$bRequest["params"]["sort"] = "publicationDate";
$bRequest["params"]["dir"] = "DESC";

$bRequest["query"]["fulltext"] = null;
$bRequest["query"]["classname"] = "video";
$bRequest["query"]["folderid"] = null;

$date 	 = date('Y-m-d H:i:s');
if($summer_time == 1)
	$newtime = strtotime($date.' + 2 hours');
else
	$newtime = strtotime($date.' + 1 hours');
$today = date('Y-m-d\TH:i:s', $newtime);
$newtime = strtotime($date . ' - 2 years');
$yesterday = date('Y-m-d\TH:i:s', $newtime);
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";

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
		$bChannels = array();
        $aResults = array();
        foreach ($channels as $channel) {
            $aChannels[] = $channel->id;
			$bChannels["$channel->id"][] = $channel->id;
			 $channels2 = bizobject::getBizobjects("channel", "siteId='".$site->id."' AND parentId=$channel->id  AND workflowstate='published'", null);
            foreach ($channels2 as $channel2) {
                $aChannels[] = $channel2->id;
				$bChannels["$channel->id"][] = $channel2->id;
            }
        }

        $bRequest["query"]["channelid"] = $rootChannel->id ."," . implode(",", $aChannels);
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        $items = $siteSearch->getResult(0, 25);
        foreach ($items as $item) {
            $aResults[] = $item;
        	echo "\t\t $item->id \t: $item->title\n";
		}

		$parameters = array(
			"site" => $site,
			"rootChannel" => $rootChannel,
			"channels" => $channels,
			"results" => $aResults, "bChannels"=>$bChannels
		);

		$generator = new wcmTemplateGenerator(null, false, wcmWidget::VIEW_CONTENT);
        $generator->executeTemplate('PUBLISH/AFPRELAXNEWS/homes/video.tpl', $parameters);
    }

}

unset($generator);
unset($siteSearch);
?>
