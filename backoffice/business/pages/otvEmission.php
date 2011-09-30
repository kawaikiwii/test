<?php
/**
 * Project:     WCM
 * File:        otvEmission.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/otvEmission', array('class' => 'otvEmission'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();


    echo '<div id="content">';

    wcmGUI::openObjectForm($bizobject, true);
    $tabs = new wcmAjaxTabs('otvEmission', true);
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/orange/otvEmission/properties'));    
	//$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media'));
    
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
