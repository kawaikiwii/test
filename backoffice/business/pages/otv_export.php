<?php
/**
 * Project:     WCM
 * File:        logicImmo_export.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */

include(WCM_DIR . '/pages/includes/header.php');
wcmGUI::renderAssetBar('OTV', 'Export');
wcmModule('business/orange/export');
include(WCM_DIR . '/pages/includes/footer.php'); ?>