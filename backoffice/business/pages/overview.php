<?php 
/**
 * Project:     WCM
 * File:        dialogs/generate.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * Dialogs enabling generation lauching and monitoring
 * Generations are launch in background (using task manager)
 */
 
// initialize system
require_once dirname(__FILE__).'../../../initWebApp.php';
$config = wcmConfig::getInstance();

// Configure Minify API
set_include_path(dirname(__FILE__).'/min/lib'.PATH_SEPARATOR.get_include_path());
require 'Minify/Build.php';
$_gc = ( require dirname(__FILE__)."/min/groupsConfig.php");

if ((isset($_REQUEST["class"])  && !empty($_REQUEST["class"])) && (isset($_REQUEST["id"])  && !empty($_REQUEST["id"])))
{
	$bizObject = new $_GET["class"]();
	$bizObject->refresh($_GET["id"]);
}
else 
{
	$bizObject = wcmMVC_Action::getContext();
}
	
$permalinks = str_replace("%format%", "detail", $bizObject->permalinks);
$filename = $config['wcm.webSite.repository'].$permalinks;

$minifyCss = new Minify_Build($_gc["afprelax.css"]);
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>OVERVIEW : <?php echo $bizObject->title?></title>
        <link rel="stylesheet" type="text/css" href="/business/pages<?php echo $minifyCss->uri('/min/m.php/afprelax.css')?>" />
        <link rel="shortcut icon" href="/rp/images/default/favicon.ico"/>
        <style type="text/css">
        	body  {
                background-color:#fff;
            }
            body, html, .ari-illustrations {
                overflow: auto;
            }
        </style>
    </head>
    <body>
        <div id="preview" class="ari-preview">
            <?php 
            //echo $filename;
            if (file_exists($filename)) {
                $str = file_get_contents($filename);
                echo($str);
            }
            ?>
        </div>
    </body>
</html>
