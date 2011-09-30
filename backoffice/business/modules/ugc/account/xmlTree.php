<?php 

require_once dirname(__FILE__).'/../../../../initWebApp.php';

$config = wcmConfig::getInstance();
$id     = getArrayParameter($_REQUEST, "id", 0);

 //$channels = channel::getArrayChannelChilds(null,6);
   // print_r($channels);   

if (!empty($id))
{
	$account = new account();
	$account->refresh($id);
	
	header("Content-type:text/xml"); 
	print("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>");
	$account->getXmlTreeStructure(null, null, array("11", "12"));;
}
else
{
	header("Content-type:text/xml"); 
	print("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>");
	
	print "<error>xml error</error>";
}
