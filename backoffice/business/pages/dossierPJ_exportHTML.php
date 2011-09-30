<?php
/**
 * Project:     WCM
 * File:        dossierPJ_exportHTML.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

include(WCM_DIR . '/pages/includes/header.php');
wcmGUI::renderAssetBar('PAGESJAUNES', 'Export HTML');
wcmModule('business/pagesjaunes/exportHTML');
include(WCM_DIR . '/pages/includes/footer.php'); ?>