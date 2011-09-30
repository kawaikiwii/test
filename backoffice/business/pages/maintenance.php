<?php

/**
 * Project:     WCM
 * File:        maintenance.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/maintenance');

    // Include header
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderAssetBar(_MENU_SYSTEM_MAINTENANCE, _MENU_SYSTEM_MAINTENANCE_PURGE_CONTENT);

    wcmModule('business/maintenance/purge');

    include(WCM_DIR . '/pages/includes/footer.php');