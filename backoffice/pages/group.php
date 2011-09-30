<?php
/*
 * Project:     WCM
 * File:        group.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('group', array('class' => 'wcmGroup', 'tree' => 'membership'));
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

    $tabs = new wcmAjaxTabs('group', true);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('system/membership/group/properties'));
    $tabs->addTab('t2', _PERMISSIONS_ACCESS, false, null, wcmModuleURL('system/permissions/properties', array('targetKind' => 'wcmMenu')));
    $tabs->addTab('t3', _PERMISSIONS_SITES, false, null, wcmModuleURL('system/permissions/properties', array('targetKind' => 'site')));
    $tabs->addTab('t4', _PERMISSIONS_GENERATION_SET, false, null, wcmModuleURL('system/permissions/properties', array('targetKind' => 'wcmGenerationSet')));
    $tabs->addTab('t5', _PERMISSIONS_BIZCLASS, false, false, wcmModuleURL('system/permissions/properties', array('targetKind' => 'wcmBizclass')));
    $tabs->addTab('t6', _PERMISSIONS_SYSCLASS, false, false, wcmModuleURL('system/permissions/properties', array('targetKind' => 'wcmSysclass')));
    
    $tabs->render($sysobject->id === 0);

    wcmGUI::closeForm();
    echo '</div>';

    include('includes/footer.php');
