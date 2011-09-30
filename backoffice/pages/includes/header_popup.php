<?php

/**
 * Project:     WCM
 * File:        _header_popup.php
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">   
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echoH8($project->title); ?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php echo $config['wcm.backOffice.url']?>skins/default/css/popup-main.css" />
    <?php include(WCM_DIR . '/js/main.js.php'); ?>
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div id="banner">
            <h1><span>Nstein WCM</span></h1>
            <ul>
                <li><a href="javascript:window.close();"><?php echo _CLOSE; ?></a></li>
            </ul>
        </div>
    </div>
    <div id="content-wrapper">
