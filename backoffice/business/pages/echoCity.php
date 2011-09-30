<?php
/**
 * Project:     WCM
 * File:        echoCity.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/echoCity', array('class' => 'echoCity'));
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
    wcmGUI::openObjectForm($bizobject,array('enctype' => 'multipart/form-data'));
    $tabs = new wcmAjaxTabs('echoCity', true);
    $tabs->addTab('t2', "Infos pratiques", true, null, wcmModuleURL('business/lesEchos/properties'));
    $tabs->addTab('t3', "Ambassade(s)", false, null, wcmModuleURL('business/lesEchos/echoEmbassy'));
    $tabs->addTab('t4', "Hopital(aux)", false, null, wcmModuleURL('business/lesEchos/echoHospital'));
    $tabs->addTab('t5', "Police(s)", false, null, wcmModuleURL('business/lesEchos/echoPolice'));
    $tabs->addTab('t6', "Aéroport(s)", false, null, wcmModuleURL('business/lesEchos/echoAirport'));
    $tabs->addTab('t7', "Diaporama", false, null, wcmModuleURL('business/lesEchos/echoSlideshow'));
    $tabs->addTab('t8', "Se loger", false, null, wcmModuleURL('business/lesEchos/echoHousing'));
    $tabs->addTab('t9', "Manger/Sortir", false, null, wcmModuleURL('business/lesEchos/echoTakeOuts'));
    $tabs->addTab('t10', "A voir/A faire", false, null, wcmModuleURL('business/lesEchos/echoMustSees'));
    $tabs->addTab('t11', "Événements", false, null, wcmModuleURL('business/lesEchos/echoEvent'));
    $tabs->addTab('t12', "Gare(s)", false, null, wcmModuleURL('business/lesEchos/echoStation'));
    $tabs->addTab('t13', "Salon(s)", false, null, wcmModuleURL('business/lesEchos/echoShow'));
    if (isset($bizobject->title)) $tabs->addTab('t14', "Synthèse", false, null, wcmModuleURL('business/lesEchos/echoSynthese'));
    if (isset($bizobject->title)) $tabs->addTab('t15', "Carte", false, null, wcmModuleURL('business/lesEchos/echoMap'));
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
