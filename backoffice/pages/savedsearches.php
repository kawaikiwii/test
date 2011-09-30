<?php
/**
 * Project:     WCM
 * File:        savedsearches.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    // Execute action
    //wcmMVC_Action::execute('savedsearches', array('class' => 'wcmSavedSearch'));
    //$bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');

    echo '<div id="assetbar">';
    echo '<h3>'._MENU_SAVED_SEARCHES.'</h3>';
    echo '</div>';

    echo '<div id="content" >';
    wcmModule('search/managelist', array('classname' => 'wcmSavedSearch'));   
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>