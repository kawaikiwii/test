<?php
/**
 * Project:     WCM
 * File:        place.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/place', array('class' => 'place'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
	
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
    $_SESSION['wcmActionMain'] = $_SESSION['wcmAction'];
    
    $multiobjectsparam = array();
    
    $relclassname = '';
    if (isset($_GET['relclassname']) && !empty($_GET['relclassname']))
    	$multiobjectsparam['relclassname'] = $_GET['relclassname'];   
    $relclassid = '';
    if (isset($_GET['relclassid']) && !empty($_GET['relclassid']))
    	$multiobjectsparam['relclassid'] = $_GET['relclassid'];
    
    if (!isset($multiobjectsparam['relclassname']) && !isset($multiobjectsparam['relclassid']))
    	$setTab = false;
    else
    	$setTab = true; 
    	 
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();
  
    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('place', true);
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/place/properties'));
	
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
