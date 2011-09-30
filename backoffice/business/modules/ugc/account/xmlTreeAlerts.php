<?php 

require_once dirname(__FILE__).'/../../../../initWebApp.php';

$config = wcmConfig::getInstance();
$id     = getArrayParameter($_REQUEST, "id", 0);
$taskId	= getArrayParameter($_REQUEST, "taskId", 0);
$source	= getArrayParameter($_REQUEST, "source", null);

 //$channels = channel::getArrayChannelChilds(null,6);
   // print_r($channels);   

if (!empty($id))
{
	$account = new account();
	$account->refresh($id);
	
	header("Content-type:text/xml"); 
	print("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>");
	// enable alert structure display and init xmltree structure
	// force Bang universe exclusion siteId 13 & 14
	$account->getXmlTreeStructure("alert", $taskId, array("7","8","9","10","11","12","13","14","15","16","17","18"), $source);
}
else
{
	header("Content-type:text/xml"); 
	print("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>");
	print "<error>xml error</error>";
}
