<?php
/**
 * Project:     WCM
 * File:        generationSet.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('generationSet', array('class' => 'wcmGenerationSet', 'tree' => 'generation'));
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

    $tabs = new wcmAjaxTabs('generationSet', true);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('system/generation/generationSet/properties'));
    $tabs->addTab('t2', _PERMISSIONS, false, null, wcmModuleURL('system/permissions/properties', array('targetKind' => 'wcmGroup')));
    $tabs->render();

    wcmGUI::closeForm();
    echo '</div>';

    include('includes/footer.php');
