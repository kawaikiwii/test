<?php
/**
 * Project:     WCM
 * File:        viaMichelin.php
 *
 * @copyright   (c)2011 Nstein Technologies
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/viaMichelin', array('class' => 'viaMichelin'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('tree', _SEARCH, true, wcmGUI::renderQuickSearchBox('className:'.$bizobject->getClass()));
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('viaMichelin', true);
    $tabs->addTab('t1', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/viaMichelin/properties'));
    $tabs->addTab('t2', "Homepage", false, null, wcmModuleURL('business/viaMichelin/homepage'));
    $tabs->addTab('t3', "Hotels", false, null, wcmModuleURL('business/viaMichelin/hotels'));
    $tabs->addTab('t4', "Restaurants", false, null, wcmModuleURL('business/viaMichelin/restaurants'));
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
