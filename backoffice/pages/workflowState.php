<?php
/*
 * Project:     WCM
 * File:        workflowState.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('workflowState', array('class' => 'wcmWorkflowState', 'tree' => 'workflow'));
    $sysobject = wcmMVC_Action::getContext();

    // Include header and menu
    include('includes/header.php');
    wcmGUI::renderObjectMenu();

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('tree', _BROWSE, true, wcmMVC_Action::getTree()->renderHTML());
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($sysobject);

    $tabs = new wcmAjaxTabs('workflowState', true);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('system/workflow/state/properties'));
    /* @todo : permissions tab
    $tabs->addTab('t2', _PERMISSIONS, true, null, wcmModuleURL('system/permissions/groups'));
    */
    $tabs->render();

    wcmGUI::closeForm();
    echo '</div>';

    include('includes/footer.php');