<?php 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

/***********************************************************

 PHP script executed by CRONTAB every 10 minutes
 @path /var/www/CRONTAB/scripts/updateObjects.php
 
 ***********************************************************/
 
//Initialize summer time
$summer_time = date("I");
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
require_once (dirname(__FILE__).'/../../../frontoffice/api/wcm.siteSearcher.php');

$session->startSession(wcmMembership::ROOT_USER_ID);

$date = date('Y-m-d H:i:s');
if($summer_time == 1)
	$newtime = strtotime($date.' + 2 hours');
else
	$newtime = strtotime($date.' + 1 hours');
$today = date('Y-m-d\TH:i:s', $newtime);
$newtime = strtotime($date.' - 2 month');
$yesterday = date('Y-m-d\TH:i:s', $newtime);
$newtime = strtotime($date.' - 1 year');
$yesterday_prevision = date('Y-m-d\TH:i:s', $newtime);

$sites = $session->getSite()->getArrayCodesSites();
foreach ($sites as $siteCode=>$siteId) {
    if ($siteCode == "logicimmo" || $siteCode == "orange") {
        break;
    }
    
    $site = new site();
    $site->refreshByCode($siteCode);
    $session->setSiteId($site->id);
    $session->setLanguage($site->language);
    
    echo "On traite le site $site->title\n\n";
    
    /*----------------------------------------
     * Dernière news publiée
     */
    
    $excludeNewsIds = array();
    
    $bRequest = array("query"=>array(), "params"=>array());
    $bRequest["params"]["ctId"] = "mainHome_lastPublishedNews";
    $bRequest["params"]["start"] = 0;
    $bRequest["params"]["limit"] = 1;
    $bRequest["params"]["sort"] = "publicationDate";
    $bRequest["params"]["dir"] = "DESC";
    $bRequest["query"]["classname"] = "news";
    $bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
    $siteSearch = new wcmSiteSearcher($bRequest);
    $siteSearch->execute();
    
    $CHANNELS_USED = array();
    
    foreach ($siteSearch->getResult(0, 0) as $item) {
        $LASTNEWS = $item;
        $excludeNewsIds[] = $item->id;
        $CHANNELS_USED[$item->channelId] = new channel($item->channelId);
        break;
    }
    
    echo "Last publish news : '$LASTNEWS->title' ($LASTNEWS->id)\n";
    
    unset($siteSearch);
    unset($bRequest);
    
    /*----------------------------------------
     * Dernières news publiées dans les autres channels
     */
    
    echo "\nOthers essential news : \n";
    $ESSENTIAL_NEWS = array();
    
    $bRequest = array("query"=>array(), "params"=>array());
    $bRequest["params"]["ctId"] = "mainHome_lastEssentialNews";
    $bRequest["params"]["start"] = 0;
    $bRequest["params"]["limit"] = 3;
    $bRequest["params"]["sort"] = "publicationDate";
    $bRequest["params"]["dir"] = "DESC";
    $bRequest["query"]["classname"] = "news";
    $bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
    $bRequest["query"]["id"] = "NOT($LASTNEWS->id)";
    
    $siteSearch = new wcmSiteSearcher($bRequest);
    $siteSearch->execute();
    
    foreach ($siteSearch->getResult(0, 2) as $item) {
        echo "\t$item->title ($item->id)\n";
        $ESSENTIAL_NEWS[] = $item;
        $excludeNewsIds[] = $item->id;
        if (!array_key_exists($item->channelId, $CHANNELS_USED)) {
            $CHANNELS_USED[$item->channelId] = new channel($item->channelId);
            
        }
    }
    
    unset($siteSearch);
    unset($bRequest);
    
    /*----------------------------------------
     * Dernières news publiées dans les thématiques
     */
    
    $bRequest = array("query"=>array(), "params"=>array());
    $bRequest["params"]["ctId"] = "mainHome_thematicNews";
    $bRequest["params"]["start"] = 0;
    $bRequest["params"]["limit"] = 3;
    $bRequest["params"]["sort"] = "publicationDate";
    $bRequest["params"]["dir"] = "DESC";
    $bRequest["query"]["classname"] = "news";
    $bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
    $LIST_THEMAS = array("luxury"=>128, "product"=>123, "celebrity"=>243, "trend"=>124);
    
    $THEMAS = array();
    $i = 0;
    
    echo "\nThematics news : \n";
    foreach ($LIST_THEMAS as $code=>$id) {
    
        $bRequest["query"]["id"] = " NOT (".implode(",", $excludeNewsIds).")";
        $bRequest["query"]["listids"] = "($id)";
        
        echo "$code : \n";
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        
        foreach ($siteSearch->getResult(0, 3) as $item) {
            echo "\t$item->title ($item->id)\n";
            $THEMAS[$i][] = $item;
            $excludeNewsIds[] = $item->id;
        }
        
        $i++;
        
    }
    
    unset($siteSearch);
    unset($bRequest);
    
    /*----------------------------------------
     * Dernière vidéo publiée
     */
    
    $bRequest = array("query"=>array(), "params"=>array());
    $bRequest["params"]["ctId"] = "mainHome_video";
    $bRequest["params"]["start"] = 0;
    $bRequest["params"]["limit"] = 1;
    $bRequest["params"]["sort"] = "publicationDate";
    $bRequest["params"]["dir"] = "DESC";
    $bRequest["query"]["classname"] = "video";
    $bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
    $siteSearch = new wcmSiteSearcher($bRequest);
    $siteSearch->execute();
    
    foreach ($siteSearch->getResult(0, 0) as $item) {
        $VIDEO = $item;
        break;
    }
    
    echo "\nLast published video : '$VIDEO->title' ($VIDEO->id)\n";
    
    unset($siteSearch);
    unset($bRequest);
    
    /*----------------------------------------
     * Dernièr diapo publiée
     */
    
    $bRequest = array("query"=>array(), "params"=>array());
    $bRequest["params"]["ctId"] = "mainHome_slideshow";
    $bRequest["params"]["start"] = 0;
    $bRequest["params"]["limit"] = 1;
    $bRequest["params"]["sort"] = "publicationDate";
    $bRequest["params"]["dir"] = "DESC";
    $bRequest["query"]["classname"] = "slideshow";
    $bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
    $siteSearch = new wcmSiteSearcher($bRequest);
    $siteSearch->execute();
    
    foreach ($siteSearch->getResult(0, 0) as $item) {
        $SLIDESHOW = $item;
        break;
    }
    
    echo "Last published diapo : '$SLIDESHOW->title' ($SLIDESHOW->id)\n";
    
    unset($siteSearch);
    unset($bRequest);
    
    if ($site->code != 'fra') {
    
        /*----------------------------------------
         * Un événement de la semaine prochaine
         */
        
        $excludeEventsIds = array();
        
        $startDate = date('Y-m-d', mktime(0, 0, 0, date('n'), date('j') + 7, date('Y')));
        $endDate = date('Y-m-d', mktime(0, 0, 0, date('n'), date('j') + 14, date('Y')));
        
        $bRequest = array("query"=>array(), "params"=>array());
        $bRequest["params"]["ctId"] = "mainHome_eventnextweek";
        $bRequest["params"]["start"] = 0;
        $bRequest["params"]["limit"] = 10;
        $bRequest["params"]["sort"] = "publicationDate";
        $bRequest["params"]["dir"] = "DESC";
        $bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
        $eventDates = "[".str_replace("-", "x", $startDate)." TO ".str_replace("-", "x", $endDate)."]";
        $eventAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $startDate)."]";
        $eventPostDates = "[".str_replace("-", "x", $endDate)." TO 2099x12x31]";
        $bRequest["query"]["classname"] = "event AND (event_startdate:$eventDates OR event_enddate:$eventDates OR (event_startdate:$eventAnteDates AND event_enddate:$eventPostDates))";
        
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        $aResults = $siteSearch->getResult(0, 10);
        
        $EVENT_NEXT_WEEK = $aResults[rand(0, sizeof($aResults) - 1)];
        $excludeEventsIds[] = $EVENT_NEXT_WEEK->id;
        
        echo "\nUn evt next week : '$EVENT_NEXT_WEEK->title' ($EVENT_NEXT_WEEK->startDate / $EVENT_NEXT_WEEK->endDate)\n";
        
        unset($siteSearch);
        unset($bRequest);
        
        /*----------------------------------------
         * Un événement du mois prochain
         */
        
        $startDate = date('Y-m-d', mktime(0, 0, 0, date('n') + 1, date('j'), date('Y')));
        $endDate = date('Y-m-d', mktime(0, 0, 0, date('n') + 2, date('j'), date('Y')));
        
        $bRequest = array("query"=>array(), "params"=>array());
        $bRequest["params"]["ctId"] = "mainHome_eventnextmonth";
        $bRequest["params"]["start"] = 0;
        $bRequest["params"]["limit"] = 10;
        $bRequest["params"]["sort"] = "publicationDate";
        $bRequest["params"]["dir"] = "DESC";
        $bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
        $bRequest["query"]["id"] = " NOT (".implode(",", $excludeEventsIds).")";
        
        $eventDates = "[".str_replace("-", "x", $startDate)." TO ".str_replace("-", "x", $endDate)."]";
        $eventAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $startDate)."]";
        $eventPostDates = "[".str_replace("-", "x", $endDate)." TO 2099x12x31]";
        $bRequest["query"]["classname"] = "event AND (event_startdate:$eventDates OR event_enddate:$eventDates OR (event_startdate:$eventAnteDates AND event_enddate:$eventPostDates))";
        
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        $aResults = $siteSearch->getResult(0, 10);
        
        $EVENT_NEXT_MONTH = $aResults[rand(0, sizeof($aResults) - 1)];
        $excludeEventsIds[] = $EVENT_NEXT_MONTH->id;
        
        echo "Un evt next month : '$EVENT_NEXT_MONTH->title' ($EVENT_NEXT_MONTH->startDate / $EVENT_NEXT_MONTH->endDate)\n";
        
        unset($siteSearch);
        unset($bRequest);
        
        /*----------------------------------------
         * Un événement lointain
         */
        
        $startDate = date('Y-m-d', mktime(0, 0, 0, date('n') + 1, date('j'), date('Y')));
        $endDate = date('Y-m-d', mktime(0, 0, 0, date('n') + 6, date('j'), date('Y')));
        
        $bRequest = array("query"=>array(), "params"=>array());
        $bRequest["params"]["ctId"] = "mainHome_eventlast";
        $bRequest["params"]["start"] = 0;
        $bRequest["params"]["limit"] = 3;
        $bRequest["params"]["sort"] = "event_enddate";
        $bRequest["params"]["dir"] = "DESC";
        $bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
        $bRequest["query"]["id"] = " NOT (".implode(",", $excludeEventsIds).")";
        
        $eventDates = "[".str_replace("-", "x", $startDate)." TO ".str_replace("-", "x", $endDate)."]";
        $eventAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $startDate)."]";
        $eventPostDates = "[".str_replace("-", "x", $endDate)." TO 2099x12x31]";
        $bRequest["query"]["classname"] = "event AND (event_startdate:$eventDates OR event_enddate:$eventDates OR (event_startdate:$eventAnteDates AND event_enddate:$eventPostDates))";
        
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        $aResults = $siteSearch->getResult(0, 3);
        
        $EVENT_LAST = $aResults[rand(0, sizeof($aResults) - 1)];
        
        echo "Un evt loin: '$EVENT_LAST->title' ($EVENT_LAST->startDate / $EVENT_LAST->endDate)\n";
        
        unset($siteSearch);
        unset($bRequest);
        
    }else{
    	/*----------------------------------------
	 * RELAXFIL ! 
         * 
	 * Une prévision la semaine prochaine
         */
        
        $excludeEventsIds = array();
        
        $startDate = date('Y-m-d', mktime(0, 0, 0, date('n'), date('j') + 7, date('Y')));
        $endDate = date('Y-m-d', mktime(0, 0, 0, date('n'), date('j') + 14, date('Y')));
        
        $bRequest = array("query"=>array(), "params"=>array());
        $bRequest["params"]["ctId"] = "mainHome_previsionnextweek";
        $bRequest["params"]["start"] = 0;
        $bRequest["params"]["limit"] = 10;
        $bRequest["params"]["sort"] = "publicationDate";
        $bRequest["params"]["dir"] = "DESC";
        $bRequest["query"]["publicationdate"] = "[$yesterday_prevision to $today]";
        $eventDates = "[".str_replace("-", "x", $startDate)." TO ".str_replace("-", "x", $endDate)."]";
        $eventAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $startDate)."]";
        $eventPostDates = "[".str_replace("-", "x", $endDate)." TO 2099x12x31]";
        //$bRequest["query"]["classname"] = "prevision AND (prevision_startdate:$eventDates OR prevision_enddate:$eventDates OR (prevision_startdate:$eventAnteDates AND prevision_enddate:$eventPostDates))";
        $bRequest["query"]["classname"] = "prevision AND prevision_startdate:$eventDates";
        
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        $aResults = $siteSearch->getResult(0, 10);
        
        $EVENT_NEXT_WEEK = $aResults[rand(0, sizeof($aResults) - 1)];
        $excludeEventsIds[] = $EVENT_NEXT_WEEK->id;
        
        echo "\nUne prévision next week : '$EVENT_NEXT_WEEK->title' ($EVENT_NEXT_WEEK->startDate / $EVENT_NEXT_WEEK->endDate)\n";
        
        unset($siteSearch);
        unset($bRequest);
	
	/*----------------------------------------
         * Une prévision du mois prochain
         */
        
        $startDate = date('Y-m-d', mktime(0, 0, 0, date('n') + 1, date('j'), date('Y')));
        $endDate = date('Y-m-d', mktime(0, 0, 0, date('n') + 2, date('j'), date('Y')));
        
        $bRequest = array("query"=>array(), "params"=>array());
        $bRequest["params"]["ctId"] = "mainHome_previsionnextmonth";
        $bRequest["params"]["start"] = 0;
        $bRequest["params"]["limit"] = 10;
        $bRequest["params"]["sort"] = "publicationDate";
        $bRequest["params"]["dir"] = "DESC";
        $bRequest["query"]["publicationdate"] = "[$yesterday_prevision to $today]";
        $bRequest["query"]["id"] = " NOT (".implode(",", $excludeEventsIds).")";
        
        $eventDates = "[".str_replace("-", "x", $startDate)." TO ".str_replace("-", "x", $endDate)."]";
        $eventAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $startDate)."]";
        $eventPostDates = "[".str_replace("-", "x", $endDate)." TO 2099x12x31]";
        $bRequest["query"]["classname"] = "prevision AND prevision_startdate:$eventDates";
        
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        $aResults = $siteSearch->getResult(0, 10);
        
        $EVENT_NEXT_MONTH = $aResults[rand(0, sizeof($aResults) - 1)];
        $excludeEventsIds[] = $EVENT_NEXT_MONTH->id;
        
        echo "Une prévision next month : '$EVENT_NEXT_MONTH->title' ($EVENT_NEXT_MONTH->startDate / $EVENT_NEXT_MONTH->endDate)\n";
        
        unset($siteSearch);
        unset($bRequest);
	
	/*----------------------------------------
         * Une prévision lointaine
         */
        
        $startDate = date('Y-m-d', mktime(0, 0, 0, date('n') + 1, date('j'), date('Y')));
        $endDate = date('Y-m-d', mktime(0, 0, 0, date('n') + 6, date('j'), date('Y')));
        
        $bRequest = array("query"=>array(), "params"=>array());
        $bRequest["params"]["ctId"] = "mainHome_previsionlast";
        $bRequest["params"]["start"] = 0;
        $bRequest["params"]["limit"] = 3;
        $bRequest["params"]["sort"] = "prevision_enddate";
        $bRequest["params"]["dir"] = "DESC";
        $bRequest["query"]["publicationdate"] = "[$yesterday_prevision to $today]";
        $bRequest["query"]["id"] = " NOT (".implode(",", $excludeEventsIds).")";
        
        $eventDates = "[".str_replace("-", "x", $startDate)." TO ".str_replace("-", "x", $endDate)."]";
        $eventAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $startDate)."]";
        $eventPostDates = "[".str_replace("-", "x", $endDate)." TO 2099x12x31]";
        $bRequest["query"]["classname"] = "prevision AND prevision_startdate:$eventDates";
        
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        $aResults = $siteSearch->getResult(0, 3);
        
        $EVENT_LAST = $aResults[rand(0, sizeof($aResults) - 1)];
        
        echo "Une prévision loin: '$EVENT_LAST->title' ($EVENT_LAST->startDate / $EVENT_LAST->endDate)\n";
        
        unset($siteSearch);
        unset($bRequest);
	
    }
    
    $ArrayObjectsStored = wcmCache::fetch('ArrayObjectsStored');
    if ( empty($ArrayObjectsStored)) {
    
        $ArrayObjectsStored = $site->storeObjects(null, false, $site->id);
    }
    
    $CHANNELS = array();
    $FOLDERS = array();
    
    foreach ($ArrayObjectsStored[$site->id]["channel"] as $id=>$channel) {
        $chan = new channel(null, $id);
        if ($chan->workflowState == 'published' && $chan->parentId != NULL) {
            $CHANNELS[] = $chan;
        }
    }
    /*
     $CHANNELS = array();
     foreach (channel::getArrayChannelChilds(NULL, $site->id, 4, 1) as $channel) {
     $chan = new channel(null, $channel["id"]);
     if ($chan->workflowState == 'published') {
     $CHANNELS[] = $chan;
     }
     }*/
	
	//$finalChannels = channel::getArrayChannelFinalChilds(0, $site->id);
    
    foreach ($ArrayObjectsStored[$site->id]["folder"] as $id=>$folder) {
        $fold = new folder(null, $id);
        if ($fold->workflowState == 'published') {
            $FOLDERS[] = $fold;
        }
        
    }
    
    $ESSENTIAL_NEWS = array_merge(array($LASTNEWS), $ESSENTIAL_NEWS);

       echo "\n\tGENERATION DE LA HOME : ";
     
    $parameters = array(/*"FINAL_CHANNELS" => $finalChannels,*/ "site"=>$site, "EVENT_LAST"=>$EVENT_LAST, "EVENT_NEXT_MONTH"=>$EVENT_NEXT_MONTH, "EVENT_NEXT_WEEK"=>$EVENT_NEXT_WEEK, "CHANNELS"=>$CHANNELS, "FOLDERS"=>$FOLDERS, "ESSENTIAL_NEWS"=>$ESSENTIAL_NEWS, "LIST_THEMAS"=>$LIST_THEMAS, "THEMAS"=>$THEMAS, "SLIDESHOW"=>$SLIDESHOW, "VIDEO"=>$VIDEO, "CHANNELS_USED"=>$CHANNELS_USED);
    $generator = new wcmTemplateGenerator(null, false, wcmWidget::VIEW_CONTENT);
    $generator->executeTemplate('PUBLISH/AFPRELAXNEWS/homes/'.$site->code.'/main.tpl', $parameters);
     echo "ok";
	 echo "\n\tGENERATION DE LA PREVIEW : ";
    $parameters = array("site"=>$site, "LASTNEWS"=>$LASTNEWS);
    $generator = new wcmTemplateGenerator(null, false, wcmWidget::VIEW_CONTENT);
    $generator->executeTemplate('PUBLISH/AFPRELAXNEWS/homes/lastNews.tpl', $parameters);
 	 echo "ok";
    $sCHANNELS = array();
    $sFOLDERS = array();
    
    $n = 0;
    foreach ($CHANNELS as $channel) {
        if ($channel->workflowState == 'published') {
            $sCHANNELS[] = $channel;
            $n++;
        }
        if ($n > 15)
            break;
    }
    
    $n = 0;
    foreach ($FOLDERS as $folder) {
        if ($folder->workflowState == 'published') {
            $sFOLDERS[] = $folder;
            $n++;
        }
        if ($n > 15)
            break;
    }
    
    $parameters = array("site"=>$site, "CHANNELS"=>$sCHANNELS, "FOLDERS"=>$sFOLDERS);
    $generator->executeTemplate('PUBLISH/AFPRELAXNEWS/homes/tagCloud.tpl', $parameters);
    
    unset($generator);
    echo "\n\n";
    
}


?>
