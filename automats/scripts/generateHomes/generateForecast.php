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
echo "## 	GENERATE HOMES FORECASTS \n";
echo "##\n";
echo "####################################################\n";
echo "\n";
echo " Début : ".date("d-m-Y H:i:s")."\n";
echo "\n";

$session->startSession(wcmMembership::ROOT_USER_ID);

$site = new site();
$site->refreshByCode("fra");
$session->setSiteId($site->id);
$session->setLanguage($site->language);

echo "Traitement du site : $site->title ($site->code / $site->id)\n";

$starDate = date("Y-m-d");
$endDate = "2013-06-26";

$forecastDates = "[".str_replace("-", "x", $starDate)." TO ".str_replace("-", "x", $endDate)."]";
$forecastAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $starDate)."]";
$forecastPostDates = "[".str_replace("-", "x", $endDate)." TO 2019x12x31]";

$date = date('Y-m-d H:i:s');
if($summer_time == 1)
	$newtime = strtotime($date.' + 2 hours');
else
	$newtime = strtotime($date.' + 1 hours');
$today = date('Y-m-d\TH:i:s', $newtime);
$newtime = strtotime($date.' - 2 years');
$yesterday = date('Y-m-d\TH:i:s', $newtime);

/*################################################
 * 	PREVISTARS
 ################################################*/
echo("################################################\n");
echo("\tPrévistars\n");
echo("------------------------------------------------\n");

$bRequest = array("query"=>array(), "params"=>array());
$bRequest["params"]["ctId"] = "mainForecast_previstars".date("YmdHMS");
$bRequest["params"]["sort"] = "forecast_enddate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
$bRequest["query"]["listids"] = 1608;
$bRequest["query"]["classname"] = "forecast  AND (forecast_startdate:$forecastDates OR forecast_enddate:$forecastDates OR (forecast_startdate:$forecastAnteDates AND forecast_enddate:$forecastPostDates))";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();

$PREVISTARS = array();
foreach ($siteSearch->getResult(0, 9) as $item) {
    $PREVISTARS[] = $item;
    echo "\t\t $item->id \t: $item->title\n";
}

unset($siteSearch);
unset($bRequest);

/*################################################
 * 	ECHEANCIER
 ################################################*/
echo("################################################\n");
echo("\tEcheancier\n");
echo("------------------------------------------------\n");

$bRequest = array("query"=>array(), "params"=>array());
$bRequest["params"]["ctId"] = "mainForecast_echeancier".date("YmdHMS");
$bRequest["params"]["sort"] = "forecast_enddate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
$bRequest["query"]["listids"] = 1609;
$bRequest["query"]["classname"] = "forecast  AND (forecast_startdate:$forecastDates OR forecast_enddate:$forecastDates OR (forecast_startdate:$forecastAnteDates AND forecast_enddate:$forecastPostDates))";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();

$ECHEANCIER = array();
foreach ($siteSearch->getResult(0, 9) as $item) {
    $ECHEANCIER[] = $item;
    echo "\t\t $item->id \t: $item->title\n";
}

unset($siteSearch);
unset($bRequest);

/*################################################
 * 	CENTER - BOX 1 - CINEMA
 ################################################*/
/*echo("################################################\n");
echo("\tCENTER - BOX 1 - CINEMA\n");
echo("------------------------------------------------\n");

$bRequest = array("query"=>array(), "params"=>array());
$bRequest["params"]["ctId"] = "mainForecast_center_box1".date("YmdHMS");
$bRequest["params"]["sort"] = "forecast_startdate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
$bRequest["query"]["channelids"] = '197';
$bRequest["query"]["classname"] = "forecast  AND (forecast_startdate:$forecastDates OR forecast_enddate:$forecastDates OR (forecast_startdate:$forecastAnteDates AND forecast_enddate:$forecastPostDates))";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();

$CENTER_BOX1 = array();
foreach ($siteSearch->getResult(0, 3) as $item) {
    $CENTER_BOX1[] = $item;
    echo "\t\t $item->id \t: $item->title\n";
}

unset($siteSearch);
unset($bRequest);*/
/*################################################
 * 	CENTER - BOX 2 - MUSIC
 ################################################*/
/*echo("------------------------------------------------\n");
echo("\tCENTER - BOX 2 - MUSIC\n");
echo("------------------------------------------------\n");

$bRequest = array("query"=>array(), "params"=>array());
$bRequest["params"]["ctId"] = "mainForecast_center_box2".date("YmdHMS");
$bRequest["params"]["sort"] = "forecast_startdate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
$bRequest["query"]["channelids"] = '196';
$bRequest["query"]["classname"] = "forecast  AND (forecast_startdate:$forecastDates OR forecast_enddate:$forecastDates OR (forecast_startdate:$forecastAnteDates AND forecast_enddate:$forecastPostDates))";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();

$CENTER_BOX2 = array();
foreach ($siteSearch->getResult(0, 3) as $item) {
    $CENTER_BOX2[] = $item;
    echo "\t\t $item->id \t: $item->title\n";
}

unset($siteSearch);
unset($bRequest);*/
/*################################################
 * 	CENTER - BOX 3 - BOOKS
 ################################################*/
/*echo("------------------------------------------------\n");
echo("\tCENTER - BOX 3 - BOOKS\n");
echo("------------------------------------------------\n");

$bRequest = array("query"=>array(), "params"=>array());
$bRequest["params"]["ctId"] = "mainForecast_center_box3".date("YmdHMS");
$bRequest["params"]["sort"] = "forecast_startdate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
$bRequest["query"]["channelids"] = '202,203,204';
$bRequest["query"]["classname"] = "forecast  AND (forecast_startdate:$forecastDates OR forecast_enddate:$forecastDates OR (forecast_startdate:$forecastAnteDates AND forecast_enddate:$forecastPostDates))";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();

$CENTER_BOX3 = array();
foreach ($siteSearch->getResult(0, 3) as $item) {
    $CENTER_BOX3[] = $item;
    echo "\t\t $item->id \t: $item->title\n";
}

unset($siteSearch);
unset($bRequest);
*/
/*################################################
 * 	RIGHT - BOX 1 - SPORT
 ################################################*/
/*echo("################################################\n");
echo("\tRIGHT - BOX 1 - SPORT\n");
echo("------------------------------------------------\n");

$bRequest = array("query"=>array(), "params"=>array());
$bRequest["params"]["ctId"] = "mainForecast_RIGHT_box1".date("YmdHMS");
$bRequest["params"]["sort"] = "forecast_startdate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
$bRequest["query"]["channelids"] = '213';
$bRequest["query"]["classname"] = "forecast  AND (forecast_startdate:$forecastDates OR forecast_enddate:$forecastDates OR (forecast_startdate:$forecastAnteDates AND forecast_enddate:$forecastPostDates))";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();

$RIGHT_BOX1 = array();
foreach ($siteSearch->getResult(0, 3) as $item) {
    $RIGHT_BOX1[] = $item;
    echo "\t\t $item->id \t: $item->title\n";
}

unset($siteSearch);
unset($bRequest);*/
/*################################################
 * 	RIGHT - BOX 2 - TOURISM
 ################################################*/
/*echo("------------------------------------------------\n");
echo("\tRIGHT - BOX 2 - TOURISM\n");
echo("------------------------------------------------\n");

$bRequest = array("query"=>array(), "params"=>array());
$bRequest["params"]["ctId"] = "mainForecast_RIGHT_box2".date("YmdHMS");
$bRequest["params"]["sort"] = "forecast_startdate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
$bRequest["query"]["channelids"] = '238,239,240,241,242,243,244,245';
$bRequest["query"]["classname"] = "forecast  AND (forecast_startdate:$forecastDates OR forecast_enddate:$forecastDates OR (forecast_startdate:$forecastAnteDates AND forecast_enddate:$forecastPostDates))";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();

$RIGHT_BOX2 = array();
foreach ($siteSearch->getResult(0, 3) as $item) {
    $RIGHT_BOX2[] = $item;
    echo "\t\t $item->id \t: $item->title\n";
}

unset($siteSearch);
unset($bRequest);*/
/*################################################
 * 	RIGHT - BOX 3 - DVDs
 ################################################*/
/*echo("------------------------------------------------\n");
echo("\tRIGHT - BOX 3 - DVDs\n");
echo("------------------------------------------------\n");

$bRequest = array("query"=>array(), "params"=>array());
$bRequest["params"]["ctId"] = "mainForecast_RIGHT_box3".date("YmdHMS");
$bRequest["params"]["sort"] = "forecast_startdate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
$bRequest["query"]["channelids"] = '210';
$bRequest["query"]["classname"] = "forecast  AND (forecast_startdate:$forecastDates OR forecast_enddate:$forecastDates OR (forecast_startdate:$forecastAnteDates AND forecast_enddate:$forecastPostDates))";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();

$RIGHT_BOX3 = array();
foreach ($siteSearch->getResult(0, 3) as $item) {
    $RIGHT_BOX3[] = $item;
    echo "\t\t $item->id \t: $item->title\n";
}

unset($siteSearch);
unset($bRequest);*/

$generator = new wcmTemplateGenerator(null, false, wcmWidget::VIEW_CONTENT);

/*$parameters = array("site"=>$site, "PREVISTARS"=>$PREVISTARS, "ECHEANCIER"=>$ECHEANCIER);
$generator->executeTemplate("PUBLISH/AFPRELAXNEWS/homes/".$site->code."/forecast.left.tpl", $parameters);*/

$parameters = array("site"=>$site, "ECHEANCIER"=>$ECHEANCIER);
$generator->executeTemplate("PUBLISH/AFPRELAXNEWS/homes/".$site->code."/forecast.left.tpl", $parameters);

/*$parameters = array("site"=>$site, "BOX1"=>$CENTER_BOX1, "BOX2"=>$CENTER_BOX2, "BOX3"=>$CENTER_BOX3);
$generator->executeTemplate("PUBLISH/AFPRELAXNEWS/homes/".$site->code."/forecast.center.tpl", $parameters);*/

/*$parameters = array("site"=>$site, "BOX1"=>$RIGHT_BOX1, "BOX2"=>$RIGHT_BOX2, "BOX3"=>$RIGHT_BOX3);
$generator->executeTemplate("PUBLISH/AFPRELAXNEWS/homes/".$site->code."/forecast.right.tpl", $parameters);*/

$parameters = array("site"=>$site, "PREVISTARS"=>$PREVISTARS);
$generator->executeTemplate("PUBLISH/AFPRELAXNEWS/homes/".$site->code."/forecast.right.tpl", $parameters);

unset($generator);

?>
