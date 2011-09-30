<?php
/**
 * Project:     WCM
 * File:        work.php
 *
 * @copyright   (c)2009 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/work', array('class' => 'work'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    $multiobjectsparam = array();
    
    $relclassname = '';
    $relclassid = '';
    
    if (isset($_GET['relclassname']) && !empty($_GET['relclassname']))
    	$multiobjectsparam['relclassname'] = $_GET['relclassname'];   

    if (isset($_GET['relclassid']) && !empty($_GET['relclassid']))
    	$multiobjectsparam['relclassid'] = $_GET['relclassid'];
    
   if (!isset($multiobjectsparam['relclassname']) && !isset($multiobjectsparam['relclassid']))
    	$setTab = false;
    else
    	$setTab = true; 
  
    // init objects to use in multi relation
    $multiobjectsparam['classFilter'] = array("photo", "person", "organisation");
    
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

    if (isset($bizobject->type))
    {
    	echo '<div id="treeview">';
	    $tabs = new wcmAjaxTabs('Informations', true);
	    $tabs->addTab('Relations', 'Relations', true, relaxGUI::createNewObjectWithRelation($bizobject, "forecast"));	
	    $tabs->addTab('Duplicate', 'Duplicate', false, '<a class="list-builder" href="javascript:wcmActionController.triggerEvent(\'save\',{clone:1});" onclick="return confirm(\'Warning : a new '.$bizobject->getClass().' object will be created and saved with actual data, please confirm!\')"><span><span>Duplicate object</span></span></a><br />');	
	    $tabs->render();
    	echo '</div>';
    }
    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('work', true);
    $tabs->addTab('_BIZ_CONTENT', _BIZ_CONTENT, true, null, wcmModuleURL('business/editorial/work/editor'));
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/work/properties'));
	//$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media'));
	
    $tabs->addTab('RELATIONS', 'RELATIONS', $setTab, null, wcmModuleURL('business/shared/multiobjects',$multiobjectsparam));
 
    $tabs->addTab('_BIZ_CATEGORIZATION', _BIZ_CATEGORIZATION, false, null, wcmModuleURL('business/shared/categorization'));
	$tabs->addTab('_BIZ_TME', _BIZ_TME, false, null, wcmModuleURL('business/tme/footprint'));

    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
