<?php
/**
 * Project:     WCM
 * File:        prevision.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/prevision', array('class' => 'prevision'));
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
    	
  	// init objects to use in multi relation
    $multiobjectsparam['classFilter'] = array("photo", "contact", "place", "personality");
    
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();
    
    if (isset($bizobject->id) && !empty($bizobject->id))
    {
    	echo '<div id="treeview">';
	    $tabs = new wcmAjaxTabs('Informations', true);
	   	$tabs->addTab('Actions', 'Actions', false, '<br /><br /><a class="list-builder" href="javascript:openDialog(\'business/pages/overview.php\',\'600\',\'800\',\'600\',\'800\',\'\');"><span><span>Overview</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="javascript:openDialog(\'business/pages/duplication.php\',\'400\',\'600\',\'400\',\'600\',\'\');"><span><span>Duplication</span></span></a><br />&nbsp;<br />');	
	    $tabs->render();
    	echo '</div>';
    }

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('prevision', true);
    $tabs->addTab('_BIZ_CONTENT', _BIZ_CONTENT, true, null, wcmModuleURL('business/editorial/prevision/editor'));
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/prevision/properties'));
	$tabs->addTab('_BIZ_CATEGORIZATION', _BIZ_CATEGORIZATION, false, null, wcmModuleURL('business/shared/categorization'));
	//$tabs->addTab('RELATIONS', 'RELATIONS', $setTab, null, wcmModuleURL('business/shared/multiobjects',$multiobjectsparam));
 	$tabs->addTab('FRELATIONS', 'RELATIONS', false, null, wcmModuleURL('business/shared/multiobjects',$multiobjectsparam));
 
    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
