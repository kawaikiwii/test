<?php

/**
 * Project:     WCM
 * File:        ajax/wcm.module.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * Ajax to display a module
 */

// Initialize the system
require_once dirname(__FILE__) . '/../initWebApp.php';

if (isset($_REQUEST['params']))
{
    $params = unserialize(urldecode($_REQUEST['params']));
} else {
    $params = array();
}

$module = getArrayParameter($_REQUEST, 'module', null);
wcmModule($module, $params);
// call_user_func_array('wcmModule', $module, $params);