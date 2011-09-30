<?php
/*
 * Project:     WCM
 * File:        log.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('log');

    // Include header
    include('includes/header.php');

    // Render command bar
    $actions = array(
        array('code' => 'refresh', 'name' => _REFRESH, 'url' => wcmMVC_Action::computeURL($session->getCurrentAction())),
        array('code' => 'refresh', 'name' => _CLEAR_TRACE_LOG, 'url' => wcmMVC_Action::computeURL($session->getCurrentAction(), 'clearTrace'))
        );
    wcmGUI::renderAssetBar(_MENU_SYSTEM_ADMINISTRATION, _MENU_SYSTEM_ADMINISTRATION_MESSAGE_LOG, $actions);

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    $tabs = new wcmAjaxTabs('group', true);
    $tabs->addTab('t1', _MESSAGE_LOG, true, null, wcmModuleURL('system/log/message'));
    $tabs->addTab('t2', _TRACE_LOG,   true, null, wcmModuleURL('system/log/trace'));
    $tabs->render();
    echo '</div>';

    include('includes/footer.php');