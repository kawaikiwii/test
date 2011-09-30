<?php 


$site = new site();
$site->refreshByCode($CURRENT_SITECODE);
$session->setSiteId($site->id);
$language = $session->getSite()->language;
$session->setLanguage($language);
// Retrieve FO Language Pack
require_once (dirname(__FILE__)."/../sites/".$site->code."/conf/lang.php");
require_once (dirname(__FILE__)."/../sites/".$site->code."/conf/config.php");

?>