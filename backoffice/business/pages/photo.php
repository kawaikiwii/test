<?php
/**
 * Project:     WCM
 * File:        photo.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/photo', array('class' => 'photo'));
    $bizobject = wcmMVC_Action::getContext();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

    echo ('<script type="text/javascript" src="includes/cropper/cropper.js"></script>');
    echo ('<script type="text/javascript" src="includes/cropper/cropper_wcm.js"></script>');

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject,array('enctype' => 'multipart/form-data'));

    $tabs = new wcmAjaxTabs('photo', true);
    //$tabs->addTab('_BIZ_OVERVIEW', _BIZ_OVERVIEW, false, null, wcmModuleURL('business/shared/overview'));
    $tabs->addTab('_BIZ_CONTENT', _BIZ_CONTENT, true, null, wcmModuleURL('business/editorial/photo/editor'));
    
	
	//$tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/photo/properties'));
	
	$tabs->addTab('_BIZ_CATEGORIZATION', _BIZ_CATEGORIZATION, false, null, wcmModuleURL('business/shared/categorization'));
	$tabs->addTab('_RLX_SPECIALFOLDERS', _RLX_SPECIALFOLDERS, false, null, wcmModuleURL('business/shared/specialfolders'));
    //$tabs->addTab('_BIZ_REFERENCING', _BIZ_REFERENCING, false, null, wcmModuleURL('business/shared/referencing'));
    $tabs->addTab('_BIZ_TME', _BIZ_TME, false, null, wcmModuleURL('business/tme/footprint'));
	    $tabs->addTab('Views', 'Views', false, null, wcmModuleURL('business/editorial/photo/views'));
    $tabs->render($bizobject->id === 0);

    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
