<?php 

if (!isset($_REQUEST["username"]) && !isset($_REQUEST["password"]) && !isset($_REQUEST["code"]) ) {
	echo "erreur" ;
	exit();
}

$url = $_SERVER["HTTP_HOST"];
$CURRENT_SITECODE = $_REQUEST["code"];

require_once (dirname(__FILE__).'/inc/wcmInit.php');
require_once (dirname(__FILE__).'/inc/siteInit.php');

$username = $_REQUEST["username"];
$password = $_REQUEST["password"];

$auth = new wcmAuthenticate($session);
$status = $auth->login($config['wcm.default.authentication'], $username, $password, NULL);

if ($session->userId) {
    $site = new site();
    $site->refreshByCode($CURRENT_SITECODE);
    $session->setSiteId($site->id);
    $language = $session->getSite()->language;
    $session->setLanguage($language);
    $session->ping();
	
	$url = $site->url;
    
    if (!$session->isAllowed($session->getSite(), wcmPermission::P_READ)) {
        wcmMVC_Action::setMessage(_LOG_SITE_FORBIDDEN);
        
    } else {
        $url = $site->url.$site->code;
    }
}

header('Location: /');
?>
