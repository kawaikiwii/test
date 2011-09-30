<?php
/**
 * Project:     WCM
 * File:        person.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/person', array('class' => 'person'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
    $_SESSION['wcmActionMain'] = $_SESSION['wcmAction'];
    
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();


    echo '<div id="content">';

    wcmGUI::openObjectForm($bizobject, true);
    $tabs = new wcmAjaxTabs('person', true);
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/person/properties'));    
	$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media'));
    
    //$tabs->addTab('_BIZ_SCHEDULES', _BIZ_SCHEDULES, false, null, wcmModuleURL('business/editorial/person/relations'));

    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
