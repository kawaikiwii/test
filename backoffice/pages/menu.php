<?php
/*
 * Project:     WCM
 * File:        menu.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

    // Execute action
    wcmMVC_Action::execute('menu', array('class' => 'wcmMenu'));
    $sysobject = wcmMVC_Action::getContext();

    // Include header and menu
    include('includes/header.php');


    // Root menu cannot be updated nor deleted
    if ($sysobject->parentId == 0 && $sysobject->id != 0)
    {
        $disabledButtons = array('delete', 'save');
    }
    else
    {
        $disabledButtons = array();
    }
    
    wcmGUI::renderObjectMenu(true, $disabledButtons);

    echo '<div id="treeview" style="background:red;">';
    $tabs = new wcmAjaxTabs('navigation', false);
    $tabs->addTab('tree', _BROWSE, true, wcmMVC_Action::getTree()->renderHTML());
    $tabs->addTab('history', _HISTORY, false, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content" style="background:blue;">';
    wcmGUI::openObjectForm($sysobject);

    $tabs = new wcmAjaxTabs('menu', true);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('system/menu/properties'));
    /**
     * @todo : manage permissions
    $tabs->addTab('t2', _PERMISSIONS, false, null, wcmModuleURL('system/permissions/groups', wcmPermission::P_EXECUTE));
     */
    $tabs->render();

    wcmGUI::closeForm();

    include('includes/footer.php');
