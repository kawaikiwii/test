<?php
/*
 * Project:     WCM
 * File:        relaxTask.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 */

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');

    wcmGUI::renderAssetBar(_MENU_SYSTEM, _MENU_SYSTEM_ADMINISTRATION_DOCUMENTS);
    wcmModule('business/documents/displayDocuments');
    
    include(WCM_DIR . '/pages/includes/footer.php');
    
