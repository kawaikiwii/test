<?php
/**
 * Project:     WCM
 * File:        article.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/article', array('class' => 'article'));
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
    // @todo :: Google keywords API panel
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('article', true);
    $tabs->addTab('t2', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/article/properties'));
    $tabs->addTab('t3', _BIZ_CONTENT, true, null, wcmModuleURL('business/editorial/article/content'));
    $tabs->addTab('t4', _BIZ_ARTICLE_INSERTS, false, null, wcmModuleURL('business/editorial/article/inserts'));
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
