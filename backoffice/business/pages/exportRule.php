<?php
/*
 * Project:     WCM
 * File:        exportRule.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 */


    // Execute action
    wcmMVC_Action::execute('business/exportRule', array('class' => 'exportRule'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();
    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', false);
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('exportRule', true);
    $tabs->addTab('t1', _BIZ_EXPORTRULE, true, null, wcmModuleURL('business/export/exportRule/properties'));
    if ($bizobject->id)
    	$tabs->addTab('t2', _DISTRIBUTION_CHANNELS, false, null, wcmModuleURL('business/export/exportRule/distributionChannels'));
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';
    include(WCM_DIR . '/pages/includes/footer.php');
    
    
