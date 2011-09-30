<?php
/**
 * Project:     WCM
 * File:        location.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/location', array('class' => 'location'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
    $_SESSION['wcmActionMain'] = $_SESSION['wcmAction'];
    
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

	
    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('location', true);
	$tabs->addTab('Infos', 'Infos', true, null, wcmModuleURL('business/editorial/location/infos'));
    //$tabs->addTab('_BIZ_OVERVIEW', _BIZ_OVERVIEW, false, null, wcmModuleURL('business/shared/overview'));
    //$tabs->addTab('_BIZ_CONTENT', _BIZ_CONTENT, true, null, wcmModuleURL('business/editorial/location/editor'));
    
  	//$tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/location/properties'));    
	$tabs->addTab('_BIZ_CATEGORIZATION', _BIZ_CATEGORIZATION, false, null, wcmModuleURL('business/editorial/location/categorization'));
	$tabs->addTab('_RLX_SPECIALFOLDERS', _RLX_SPECIALFOLDERS, false, null, wcmModuleURL('business/shared/specialfolders'));
	$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media'));

    //$tabs->addTab('_BIZ_REFERENCING', _BIZ_REFERENCING, false, null, wcmModuleURL('business/shared/referencing'));
    //$tabs->addTab('_BIZ_TME', _BIZ_TME, false, null, wcmModuleURL('business/tme/footprint'));

    //Do not display those tabs for the moment
    /*
    $tabs->addTab('t8', _BIZ_DESIGN, false, null, wcmModuleURL('business/shared/design'));
    $tabs->addTab('t9', _BIZ_VERSIONS, false, null, wcmModuleURL('business/shared/versioning'));
    $tabs->addTab('t10', _BIZ_CONTRIBUTIONS, false, null, wcmModuleURL('business/shared/contributions'));
    */

    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
