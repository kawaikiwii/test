<?php
/*
 * Project:     WCM
 * File:        bizclass.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('bizclass', array('class' => 'wcmBizclass'));
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

    $tabs = new wcmAjaxTabs('bizclass', true);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('system/bizlogic/bizclass/properties'));
    $tabs->addTab('t2', _PERMISSIONS, false, null, wcmModuleURL('system/permissions/properties', array('targetKind' => 'wcmGroup')));

    $tabs->render();

    wcmGUI::closeForm();
    echo '</div>';

    include('includes/footer.php');
