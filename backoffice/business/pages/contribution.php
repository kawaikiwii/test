<?php

/**
 * Project:     WCM
 * File:        contribution.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/contribution', array('class' => 'contribution'));
    $bizobject = wcmMVC_Action::getContext();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu(true, array("save"));

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('tree', _SEARCH, true, wcmGUI::renderQuickSearchBox('className:'.$bizobject->getClass()));
    $tabs->addTab('browse', _BROWSE, true, wcmGUI::renderBrowsePanel());
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';


    echo '<div id="content">';

    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('contribution', true);
    $tabs->addTab('t1', _BIZ_OVERVIEW, true, null, wcmModuleURL('business/shared/overview'));
    $tabs->addTab('t2', _BIZ_CATEGORIZATION, false, null, wcmModuleURL('business/shared/categorization'));
    $tabs->addTab('t3', _BIZ_TME, false, null, wcmModuleURL('business/tme/footprint'));
    $tabs->render();
    wcmGUI::closeForm();

    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>