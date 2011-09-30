<?php
/**
 * Project:     WCM
 * File:        site.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('business/site', array('class' => 'site'));
    $bizobject = wcmMVC_Action::getContext();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('tree', _SEARCH, true, wcmGUI::renderQuickSearchBox('className:'.$bizobject->getClass(), 'quickSearch',10,'quickSearch', 1));
    $tabs->addTab('browse', _BROWSE, true, wcmGUI::renderBrowsePanel());
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);

    $tabs = new wcmAjaxTabs('site', true);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('business/website/site/properties'));
    // @todo : add persimissions tab
    $tabs->addTab('t2', _PERMISSIONS, false, null, wcmModuleURL('system/permissions/properties', array('targetKind' => 'site')));
    $tabs->addTab('t3', _BIZ_SERVICES, false, null, wcmModuleURL('business/website/site/services'));
    $tabs->render();

    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');