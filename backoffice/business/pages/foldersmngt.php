<?php
/**
 * Project:     WCM
 * File:        /business/pages/foldersmngt.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 * @author jmeyer@relaxnews.com
 */

    // Execute action
    wcmMVC_Action::execute('business/foldersmngt', array('class' => 'channel'));
    $bizobject = wcmMVC_Action::getContext();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    //wcmGUI::renderObjectMenu();

   /* echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('tree', _SEARCH, true, wcmGUI::renderQuickSearchBox('className:'.$bizobject->getClass(), 'quickSearch',10,'quickSearch', 1));
    $tabs->addTab('browse', _BROWSE, true, wcmGUI::renderBrowsePanel());
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';*/

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);

    $tabs = new wcmAjaxTabs('Folders', true);
    $tabs->addTab('Folders', _PROPERTIES, true, null, wcmModuleURL('business/editorial/folders/properties'));

    $tabs->render($bizobject->id === 0);

    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
