<?php

/*
 * Project:     WCM
 * File:        controller.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Retrieve relative path of PHP Ajax handler
$ajaxHandler = getArrayParameter($_REQUEST, "ajaxHandler", '');

// Delegate to the handler
require(WCM_DIR . '/business/ajax/' . $ajaxHandler . '.php');

?>
