<?php
/*
 * Project:     WCM
 * File:        content.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('connector', array('class' => 'wcmConnector'));
    $sysobject = wcmMVC_Action::getContext();

    // Include header and menu
    include('includes/header.php');
    wcmGUI::renderObjectMenu();

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', false);
    $tabs->addTab('tree', _BROWSE, true, wcmMVC_Action::getTree()->renderHTML());
    $tabs->addTab('history', _HISTORY, false, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($sysobject);

    $tabs = new wcmAjaxTabs('connector', false);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('system/connector/properties'));
    $tabs->render();

    wcmGUI::closeForm();
    echo '</div>';

    include('includes/footer.php');
