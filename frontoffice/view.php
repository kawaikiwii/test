<?php
// Initialize WCM API
require_once (dirname(__FILE__).'/inc/wcmInit.php');
if (!(isset($session->userId) && $session->userId)) {
	header('Location: index.php');
    exit();
}
$session->ping();
$CURRENT_SITECODE = $session->getSite()->code;
require_once (dirname(__FILE__).'/inc/siteInit.php');

$classname = isset($_REQUEST["classname"]) ? $_REQUEST["classname"] : null;
$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : null;

if (!$classname AND !$id) {
    echo "WRONG PARAMETERS";
    exit();
}

if($id != "denied") {
	$bizObject = new $classname($project, $id);

	if ($bizObject->isAllowed("primaire","children")) {

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
	        $keywords = preg_replace('@[^a-zA-Z0-9_\"\sàâäçèéêëîïôöùûüÀÂÄÇÈÉÊËÎÏÔÖÙÛÜ]@', '', $keywords);

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
	    }

	    $minifyCss = new Minify_Build($_gc["afprelax.css"]);

	?>
	<html>
	    <head>
	        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	        <title><?php echo APP_NAME?>: <?php echo $bizObject->title?></title>
	        <link rel="stylesheet" type="text/css" href="<?php echo $minifyCss->uri('/min/m.php/afprelax.css')?>" />
	        <link rel="shortcut icon" href="/rp/images/default/favicon.ico"/>
	        <style type="text/css">
	            body, html, .ari-illustrations {
	                overflow: auto;
	            }
	        </style>
	    </head>
	    <body style="background-color:#fff;">
	        <div id="preview" class="ari-preview">
	            <?php echo($str); ?>
	        </div>
	    </body>
	</html>
	<?php
	}
	else {
	    @ include (dirname(__FILE__)."/sites/$site->code/restricted.php");
	}
}
else {
	@ include (dirname(__FILE__)."/../sites/$site->code/restricted.php");
}
?>