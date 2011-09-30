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

$from = (isset($_REQUEST["from"]) && $_REQUEST["from"] != "0") ? $_REQUEST["from"] : date("Y-m-d\T00:00:00", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
//$now  = date("Y-m-d\TH:i:s");
//$now = date("Y-m-d\TH:i:s", mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y")));

$date = date('Y-m-d H:i:s');
if($summer_time == 1)
	$newtime = strtotime($date.' + 2 hours');
else
	$newtime = strtotime($date.' + 1 hours');

$now = date('Y-m-d\TH:i:s', $newtime);
//echo "[$from to $now]";
$bRequest["params"]["ctId"] = "refresh-".uniqid();
$bRequest["params"]["start"] = 0;
$bRequest["params"]["limit"] = 100;
$bRequest["params"]["sort"] = "publicationDate";
$bRequest["params"]["dir"] = "ASC";
$bRequest["params"]["classname"] = "news,notice,event,slideshow,video";

$bRequest["query"]["publicationdate"] = "[$from to $now]";

$siteSearch = new wcmSiteSearcher($bRequest);
$siteSearch->execute();
echo $siteSearch->getResultJSON(0, 100, true);
?>
