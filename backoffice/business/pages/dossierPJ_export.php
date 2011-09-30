<?php
/**
 * Project:     WCM
 * File:        dossierPJ_export.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

include(WCM_DIR . '/pages/includes/header.php');
wcmGUI::renderAssetBar('PAGESJAUNES', 'Export');
wcmModule('business/pagesjaunes/export');
include(WCM_DIR . '/pages/includes/footer.php'); ?>