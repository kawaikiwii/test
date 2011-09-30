<?php
/**
 * Project:     WCM
 * File:        moderation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/moderation');

    // Include header and script
    include(WCM_DIR . '/pages/includes/header.php');
    echo '<script language="javascript" type="text/javascript">' .
         '   function onSelectItem(className, idObjet) {' .
         '       window.location.href = "index.php?_wcmAction="+className+"&id="+idObjet;' .
         '       return; }' .
         '</script>';

    // Create tabs pane
    $tabs = new wcmTabs();
    $tabs->startPane('moderation');

    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', true);
    $tabs->addTab('tree', _SEARCH, true, wcmGUI::renderQuickSearchBox('className:'.$bizobject->getClass()));
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';


    $tabs->endPane();

    include(WCM_DIR . '/pages/includes/footer.php');
?>