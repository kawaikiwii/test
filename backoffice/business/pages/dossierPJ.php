<?php
/**
 * Project:     WCM
 * File:        dossierPJ.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/dossierPJ', array('class' => 'dossierPJ'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
	
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
    $_SESSION['wcmActionMain'] = $_SESSION['wcmAction'];
    	 
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();
  
    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('dossier', true);
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/pagesjaunes/properties'));
	
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
