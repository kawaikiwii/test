<?php

/*
 * Project:     WCM
 * File:        controller.js.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// No browser cache
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// JavaScript output
header('Content-Type: text/javascript; charset=UTF-8');

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// Create Sys Ajax controller
echo "var wcmSysAjaxController = new NcmAjaxController(" .
     "    'wcmSysAjaxController', " .
     "    '".$config['wcm.backOffice.url']."ajax', " .
     "    'controller'" . ");";

// Create Biz Ajax controller
echo "var wcmBizAjaxController = new NcmAjaxController(" .
     "    'wcmBizAjaxController', " .
     "    '".$config['wcm.backOffice.url']."business/ajax', " .
     "    'controller'" . ");";

?>
