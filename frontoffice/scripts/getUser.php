<?php 
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
	
	$names = explode('|', $CURRENT_USER->name);

	$data[] = array(
			'first' => $names[0],
			'last' => $names[1],
			'company' => $CURRENT_ACCOUNT->companyName,
			'email' => $CURRENT_USER->email,
			'expirationdate' => $CURRENT_ACCOUNT->expirationDate
			);
						
	// Note that json_encode() wraps the data in [ ] and escapes slashes in dates, both of
	// which will cause problems in the Ext reader unless you make your own reader
	// The following hack is simply to demonstrate how you could get around this to return
	// the same format as the native reader is expecting. 
	// BUT a real example will need to cope with unexpected characters such as embedded 
	// double or single quotes etc
	$tmpData = json_encode($data);
	$tmpData = substr($tmpData,1,strlen($tmpData)-2); // strip the [ and ]
	$tmpData = str_replace("\\/","/",'{success:true,data:'.$tmpData.'}'); // unescape the slashes

	$result = $tmpData; 


echo $tmpData;
?>

