<?php
/*
 * Project:     WCM
 * File:        sysclass.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('sysclass', array('class' => 'wcmSysclass'));
    $sysobject = wcmMVC_Action::getContext();

    // Include header and menu
    include('includes/header.php');

	$disabledButtons = array('delete');
	if($sysobject->id == 0) $disabledButtons[] = 'save';

    wcmGUI::renderObjectMenu(true, $disabledButtons);

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', false);
    $tabs->addTab('tree', _BROWSE, true, wcmMVC_Action::getTree()->renderHTML());
    $tabs->addTab('history', _HISTORY, false, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($sysobject);

    $tabs = new wcmAjaxTabs('sysclass', true);
    $tabs->addTab('t1', _SYSCLASS, true, null, wcmModuleURL('system/bizlogic/sysclass/properties'));
    $tabs->addTab('t2', _PERMISSIONS, false, null, wcmModuleURL('system/permissions/properties', array('targetKind'=>'wcmGroup')));

    $tabs->render($sysobject->id === 0);

    wcmGUI::closeForm();
    echo '</div>';

    include('includes/footer.php');
