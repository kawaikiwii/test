<?php 
//Initialize summer time
$summer_time = date("I");
// Initialize WCM API
require_once (dirname(__FILE__).'/../inc/wcmInit.php');
if (!($session->userId)) {
    header("location: /");
    exit();
}
$site = $session->getSite();
$session->setLanguage($site->language);
// Retrieve FO Language Pack
require_once (dirname(__FILE__)."/../sites/".$site->code."/conf/lang.php");

$bRequest = array("query"=>array(), "params"=>array());

$bRequest["params"]["ctId"] = (isset($_REQUEST["ctId"]) && $_REQUEST["ctId"] != "") ? $_REQUEST["ctId"] : 0;
$bRequest["params"]["start"] = (isset($_REQUEST["start"]) && $_REQUEST["start"] != "") ? $_REQUEST["start"] : 0;
$bRequest["params"]["limit"] = (isset($_REQUEST["limit"]) && $_REQUEST["limit"] != "") ? $_REQUEST["limit"] : 25;
$bRequest["params"]["sort"] = (isset($_REQUEST["sort"]) && $_REQUEST["sort"] != "") ? $_REQUEST["sort"] : null;
$bRequest["params"]["dir"] = (isset($_REQUEST["dir"]) && $_REQUEST["dir"] != "") ? $_REQUEST["dir"] : null;

$bRequest["query"]["fulltext"] = (isset($_REQUEST["query"]) && $_REQUEST["query"] != "") ? $_REQUEST["query"] : null;
if(isset($_REQUEST["channelIds"]) && $_REQUEST["channelIds"] != "")
	$bRequest["query"]["channelids"] = $_REQUEST["channelIds"];
elseif(isset($_REQUEST["rubric"]) && $_REQUEST["rubric"] != "")
	$bRequest["query"]["channelids"] = $_REQUEST["rubric"];
else
	$bRequest["query"]["channelids"] = null;
$bRequest["query"]["classname"] = (isset($_REQUEST["classname"]) && $_REQUEST["classname"] != "") ? $_REQUEST["classname"] : "news,video,slideshow,prevision,event OR (classname:notice AND referentclass:NOT(slideshow,event,video))";
$bRequest["query"]["folderid"] = (isset($_REQUEST["folders"]) && $_REQUEST["folders"] != "") ? $_REQUEST["folders"] : null;
$bRequest["query"]["listids"] = (isset($_REQUEST["listIds"]) && $_REQUEST["listIds"] != "") ? $_REQUEST["listIds"] : null;
$bRequest["query"]["accountPermission"] = (isset($_REQUEST["accountPermission"])) ? $_REQUEST["accountPermission"] : true;
$bRequest["query"]["accountPermission"] = filter_var($bRequest["query"]["accountPermission"], FILTER_VALIDATE_BOOLEAN);
if(isset($_REQUEST["externalFolders"]) && $_REQUEST["externalFolders"] != "") {
	if(substr($_REQUEST["externalFolders"],0,1) != 0) {
		$folder = new folder();
		$folder->refresh($_REQUEST["externalFolders"]);
		$bRequest["query"]["siteid"] = $folder->siteId;
		$bRequest["query"]["folderid"] = $_REQUEST["externalFolders"];
	}
	else
		$bRequest["query"]["siteid"] = substr($_REQUEST["externalFolders"],2);
	$bRequest["query"]["accountPermission"] = false;
}

/*$today = date("Y-m-d\TH:i:s", mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y")));
 //$yesterday = today - 26 months
 $yesterday = date("Y-m-d\T00:00:00", mktime(0, 0, 0, date("m") - 26, date("d"), date("Y")));
 */
 
$date = date('Y-m-d H:i:s');
if($summer_time == 1)
	$newtime = strtotime($date.' + 2 hours');
else
	$newtime = strtotime($date.' + 1 hours');
$today = date('Y-m-d\TH:i:s', $newtime);
$newtime = strtotime($date.' - 36 month');
$yesterday = date('Y-m-d\TH:i:s', $newtime);

$bRequest["query"]["publicationdate"] = "[$yesterday to $today]";

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
    $country = (isset($_REQUEST["country"]) && $_REQUEST["country"] != "") ? $_REQUEST["country"] : null;
    if(!empty($country))
    	$bRequest["query"]["classname"] .= " AND event_country:$country";
    $city = (isset($_REQUEST["city"]) && $_REQUEST["city"] != "") ? $_REQUEST["city"] : null;
    if(!empty($city)) {
    	$url = "http://api.geonames.org/search?q=".rawurlencode(utf8_encode($city)) ."&lang=en&username=spajot";
		$page = file_get_contents($url);
		$xml = new SimpleXMLElement($page);
		$city = ($xml->geoname->name != "") ? $xml->geoname->name : $city;
    	$bRequest["query"]["classname"] .= ' AND event_city:('.$city.')';
	}
    
    if($bRequest["params"]["sort"] == null || strtolower($bRequest["params"]["sort"]) == "publicationdate")
    	$bRequest["params"]["sort"] = "event_startdate";
    $bRequest["query"]["accountPermission"] = false;
}

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
    $ratingValue = (isset($_REQUEST["ratingValue"]) && $_REQUEST["ratingValue"] != "") ? $_REQUEST["ratingValue"] : null;
    if(!empty($ratingValue))
    	$bRequest["query"]["classname"] .= " AND ratingValue:$ratingValue";
    
    if($bRequest["params"]["sort"] == null || strtolower($bRequest["params"]["sort"]) == "publicationdate")
    	$bRequest["params"]["sort"] = "prevision_startdate";
    $bRequest["query"]["accountPermission"] = false;
}

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();
echo $siteSearch->getResultJSON($bRequest["params"]["start"], $bRequest["params"]["limit"]);

?>
