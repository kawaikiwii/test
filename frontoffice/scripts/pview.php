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
ini_set('error_reporting', E_ERROR);
$classname = isset($_REQUEST["classname"]) ? $_REQUEST["classname"] : null;
$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : null;

if (!$classname AND !$id) {
    echo "WRONG PARAMETERS";
    exit();
}

if($id != "denied") {
	$bizObject = new $classname($project, $id);
	
	if ($bizObject->isAllowed("primaire","children") || count($bizObject->getAssoc_categorization()) == 0) {
	
	    $permalinks = str_replace("%format%", "detail", $bizObject->permalinks);
	    $filename = $config['wcm.webSite.repository'].$permalinks;
	    
	    if (!file_exists($filename)) {
	        @$bizObject->generate();
	    }
	    
	    if (!file_exists($filename)) {
	        echo("ERROR : file was not found : ".$permalinks);
	        exit;
	    }
	    
	    $str = file_get_contents($filename);
	    if (isset($_REQUEST["query"]) && $_REQUEST["query"] != "") {
	        $keywords = trim($_REQUEST["query"]);
	        $keywords = preg_replace('@[^a-zA-Z0-9_\"\sàâäçèéêëîïôöùûüÀÂÄÇÈÉÊËÎÏÔÖÙÛÜ%]@', '', $keywords);
	        
	        if ($keywords != "") {
	            $str = preg_replace("#\b(".implode("|", preg_split("/[\s,]+/", $keywords)).")\b#i", "<span class='ari-search-hit'>$0</span>", $str);
	            $str = preg_replace("#(<a[^>]*)<span class='ari\-search\-hit'>#", "$1", $str);
	            $str = preg_replace("#(<a[^>]*)</span>#", "$1", $str);
	            $str = preg_replace("#(<img[^>]*)<span class='ari\-search\-hit'>#", "$1", $str);
	            $str = preg_replace("#(<img[^>]*)</span>#", "$1", $str);
	            $str = preg_replace("#(<img[^>]*)<span class='ari\-search\-hit'>#", "$1", $str);
	            $str = preg_replace("#(<img[^>]*)</span>#", "$1", $str);
	            $str = preg_replace("#(<[^>]*)<span class='ari\-search\-hit'>#", "$1", $str);
	            $str = preg_replace("#(<[^>]*)</span>#", "$1", $str);
	        }
	        
	        /*foreach ($keywords as $keyword) {
	            //$str = preg_replace("/(?!<!--)(?!<)(^|[\s\.,>])($keyword)($|[\s,\.])(?!>)(?!-->)/", "$1<span class='ari-search-hit'>$2</span>$3", $str);
	        }*/
	        
	    }
	    echo($str);
	} else {
	    @ include (dirname(__FILE__)."/../sites/$site->code/restricted.php");
	}
}
else {
	@ include (dirname(__FILE__)."/../sites/$site->code/restricted.php");
}
?>
