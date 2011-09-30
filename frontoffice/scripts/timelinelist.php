<?php 
// Initialize WCM API
require_once (dirname(__FILE__).'/../inc/wcmInit.php');
if (!(isset($session->userId) && $session->userId)) {
    exit();
}
$site = $session->getSite();
$session->setLanguage($site->language);
// Retrieve FO Language Pack
require_once (dirname(__FILE__)."/../sites/".$site->code."/conf/lang.php");

$bRequest = array("query"=>array(), "params"=>array());

$bRequest["params"]["ctId"]		= (isset($_REQUEST["ctId"]) && $_REQUEST["ctId"] != "") ? $_REQUEST["ctId"] : 0;
$bRequest["params"]["start"]	= 0;
$bRequest["params"]["limit"]	= 500;
$bRequest["params"]["dir"]		= "ASC";

$bRequest["query"]["fulltext"] = (isset($_REQUEST["query"]) && $_REQUEST["query"] != "") ? $_REQUEST["query"] : null;
if(isset($_REQUEST["channelIds"]) && $_REQUEST["channelIds"] != "")
	$bRequest["query"]["channelids"] = $_REQUEST["channelIds"];
elseif(isset($_REQUEST["rubric"]) && $_REQUEST["rubric"] != "")
	$bRequest["query"]["channelids"] = $_REQUEST["rubric"];
else
	$bRequest["query"]["channelids"] = null;

$previsionDates = "";
if ((isset($_REQUEST["previsionStartDate"])) && (isset($_REQUEST["previsionEndDate"]))) {
	$quand = $_REQUEST["quand"];
	$previsionDates = "[".str_replace("-", "x", $_REQUEST["previsionStartDate"])." TO ".str_replace("-", "x", $_REQUEST["previsionEndDate"])."]";
    $previsionAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $_REQUEST["previsionStartDate"])."]";
    $previsionPostDates = "[".str_replace("-", "x", $_REQUEST["previsionEndDate"])." TO 2099x12x31]";
    $bRequest["query"]["classname"] = "prevision AND (";
    if($quand == "start")
    	$bRequest["query"]["classname"] .= "prevision_startdate:$previsionDates";
    elseif($quand == "end")
    	$bRequest["query"]["classname"] .= "prevision_enddate:$previsionDates";
    else
    	$bRequest["query"]["classname"] .= "prevision_startdate:$previsionDates OR prevision_enddate:$previsionDates OR (prevision_startdate:$previsionAnteDates AND prevision_enddate:$previsionPostDates)";
    $bRequest["query"]["classname"] .= ")";
    $ratingValue = (isset($_REQUEST["note"]) && $_REQUEST["note"] != "") ? $_REQUEST["note"] : null;
    if(!empty($ratingValue))
    	$bRequest["query"]["classname"] .= " AND ratingValue:$ratingValue";
	
    $bRequest["query"]["listids"] = (isset($_REQUEST["types"]) && $_REQUEST["types"] != "") ? $_REQUEST["types"] : null;
    
    $bRequest["params"]["sort"] = "prevision_startdate";
    
    $bRequest["query"]["accountPermission"] = false;
}

$eventDates = "";
if ((isset($_REQUEST["eventStartDate"])) && (isset($_REQUEST["eventEndDate"]))) {
	$quand = $_REQUEST["quand"];
	$eventDates = "[".str_replace("-", "x", $_REQUEST["eventStartDate"])." TO ".str_replace("-", "x", $_REQUEST["eventEndDate"])."]";
    $eventAnteDates = "[2009x01x01 TO ".str_replace("-", "x", $_REQUEST["eventStartDate"])."]";
    $eventPostDates = "[".str_replace("-", "x", $_REQUEST["eventEndDate"])." TO 2099x12x31]";
    $bRequest["query"]["classname"] = "event AND (";
    if($quand == "start")
    	$bRequest["query"]["classname"] .= "event_startdate:$eventDates";
    elseif($quand == "end")
    	$bRequest["query"]["classname"] .= "event_enddate:$eventDates";
    else
    	$bRequest["query"]["classname"] .= "event_startdate:$eventDates OR event_enddate:$eventDates OR (event_startdate:$eventAnteDates AND event_enddate:$eventPostDates)";
    $bRequest["query"]["classname"] .= ")";
	
	$city = (isset($_REQUEST["city"]) && $_REQUEST["city"] != "") ? $_REQUEST["city"] : null;
    if(!empty($city))
    	$bRequest["query"]["classname"] .= ' AND event_city:"'.$city.'"';
    
    $bRequest["params"]["sort"] = "event_startdate";
    
    $bRequest["query"]["accountPermission"] = false;
}

$bRequest["query"]["accountPermission"] = false;

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();
foreach ($siteSearch->bizsearch->getDocumentRange($bRequest["params"]["start"], $bRequest["params"]["start"] + $bRequest["params"]["limit"], $siteSearch->id, false) as $item) {
	if (trim($item->title) != "") {
		if(!empty($previsionDates))
			$bizObject = new prevision($project, $item->id);
		if(!empty($eventDates))
			$bizObject = new event($project, $item->id);
		$permalinks = str_replace("%format%", "detail", $bizObject->permalinks);
		$filename = $config['wcm.webSite.repository'].$permalinks;
		$str = file_get_contents($filename);
		$str = str_replace("<div class=\"ari-illustrations\">", "<div style=\"display:none;\">", $str);
		$str = str_replace("<div class=\"ari-content\">", "<div style=\"font-size:1.2em;font-size:1.2em;padding:0.5em;\">", $str);
		$str = str_replace("<div class=\"ari-informations\">", "<div>", $str);
		echo "<div id='preview' class='ari-preview'>".$str."</div>";
	}
}
?>
