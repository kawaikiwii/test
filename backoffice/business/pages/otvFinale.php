<?php
/**
 * Project:     WCM
 * File:        otvFinale.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/otvFinale', array('class' => 'otvFinale'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

    echo '<div id="content">';

    wcmGUI::openObjectForm($bizobject, true);
    $tabs = new wcmAjaxTabs('otvPortrait', true);
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/orange/otvFinale/properties'));    
	//$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media'));
    
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
