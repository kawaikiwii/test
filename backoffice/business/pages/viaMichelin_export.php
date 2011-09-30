<?php
/**
 * Project:     WCM
 * File:        lesechos_export.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */

include(WCM_DIR . '/pages/includes/header.php');
wcmGUI::renderAssetBar('VIAMICHELIN', 'Export');
wcmModule('business/viaMichelin/export');
include(WCM_DIR . '/pages/includes/footer.php'); ?>