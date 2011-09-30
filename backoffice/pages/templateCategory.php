<?php
/**
 * Project:     WCM
 * File:        templateCategory.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('templateCategory', array('class' => 'wcmTemplateCategory', 'tree' => 'template'));
    $sysobject = wcmMVC_Action::getContext();

    // Include header and menu
    include('includes/header.php');
    wcmGUI::renderObjectMenu(true, array('lock', 'unlock'));

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('tree', _BROWSE, true, wcmMVC_Action::getTree()->renderHTML());
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($sysobject);

    $tabs = new wcmAjaxTabs('templateCategory', true);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('system/generation/templateCategory/properties'));
    $tabs->render();

    wcmGUI::closeForm();
    echo '</div>';

    include('includes/footer.php');