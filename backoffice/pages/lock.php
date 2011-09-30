<?php
/*
 * Project:     WCM
 * File:        lock.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('lock', array('class' => 'wcmLock'));

    // Include header
    include('includes/header.php');

    wcmGUI::renderAssetBar(_MENU_SYSTEM_MAINTENANCE, _MENU_SYSTEM_MAINTENANCE_UNLOCK_CONTENT);

    // Render lock dashboard
    $dashboard = new wcmDashboard(WCM_DIR . '/xml/lock/dashboard.xml');
    echo $dashboard->render();

    include('includes/footer.php');