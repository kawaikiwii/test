<?php
/**
 * Project:     WCM
 * File:        poll.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/poll', array('class' => 'poll'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('tree', _SEARCH, true, wcmGUI::renderQuickSearchBox('className:'.$bizobject->getClass()));
    $tabs->addTab('browse', _BROWSE, true, wcmGUI::renderBrowsePanel());
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';


    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);

    $tabs = new wcmAjaxTabs('poll', true);
    $tabs->addTab('t1', _BIZ_OVERVIEW, false, null, wcmModuleURL('business/shared/overview'));
    $tabs->addTab('t2', _PROPERTIES, false, null, wcmModuleURL('business/ugc/poll/properties'));
    $tabs->addTab('t3', _BIZ_CONTENT, true, null, wcmModuleURL('business/ugc/poll/content'));
    $tabs->render($bizobject->id === 0);

    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
