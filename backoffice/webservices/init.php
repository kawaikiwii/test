<?php

/**
 * Project:     WCM
 * File:        webservices/init.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM Web services root directory
 */
define('WCM_WS_DIR', dirname(__FILE__));

// Initialize the WCM API
require_once WCM_WS_DIR . '/../initApi.php';

// Load the Web services configuration
require_once WCM_WS_DIR . '/config.php';

// Load localized Web service messages
require_once WCM_WS_DIR . '/messages.php';

?>