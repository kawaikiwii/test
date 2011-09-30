<?php

/**
 * Project:     WCM
 * File:        wcm_templateselector.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// Display module
$field = getArrayParameter($_REQUEST, "field", 'categoryId');

include(WCM_DIR . '/pages/includes/header_popup.php');
wcmModule('mod_wcmcategorybrowser', array($field));
include(WCM_DIR . '/pages/includes/footer.php');