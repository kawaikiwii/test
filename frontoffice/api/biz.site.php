<?php 
function getDefaultSiteCode() {

	// Looking for relaxfil access
    $pos = strpos($_SERVER["HTTP_HOST"], "relaxfil");
    if (!$pos === false) {
        return ("fra");
    }
    
	// Seems to want to connect to AFP/RELAX
	
	// If code is "fra" redirect to AFP/RELAX in French
	if(isset($_REQUEST["code"])) {
		$code = $_REQUEST["code"];
		
		if ($code == "fra") {
			header("Location: /fr");
		}
		return ($code);
	}
	
	// Looking for french access to international newswire
	 if(!isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
	     return ("en");
    $pos = strpos($_SERVER["HTTP_ACCEPT_LANGUAGE"], "fr;");
    if (!$pos === false) {
        return ("fr");
    }
    
	// Default access to english version of afprelaxnews.com
    return ("en");
}


?>
