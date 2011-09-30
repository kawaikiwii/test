<?php
/**
 * Project:     WCM
 * File:        event.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/event', array('class' => 'event'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
    $_SESSION['wcmActionMain'] = $_SESSION['wcmAction'];
    
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

    if (isset($bizobject->id) && !empty($bizobject->id))
    {
    	echo '<div id="treeview">';
	    $tabs = new wcmAjaxTabs('Informations', true);
	    //if ($bizobject->workflowState == "published")
	    //{
	    	$tabs->addTab('Actions', 'Actions', false, '<br /><br /><a class="list-builder" href="javascript:openDialog(\'business/pages/overview.php\',\'768\',\'1024\',\'768\',\'1024\',\'\');"><span><span>Overview</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="javascript:openDialog(\'business/pages/duplication.php\',\'400\',\'600\',\'400\',\'600\',\'\');"><span><span>Duplication</span></span></a><br />&nbsp;<br />');	
	    //}
	    $tabs->render();
    	echo '</div>';
    }

    echo '<div id="content">';

    wcmGUI::openObjectForm($bizobject, true);
    $tabs = new wcmAjaxTabs('event', true);
    //$tabs->addTab('_BIZ_OVERVIEW', _BIZ_OVERVIEW, false, null, wcmModuleURL('business/shared/overview'));
    $tabs->addTab('_BIZ_CONTENT', _BIZ_CONTENT, true, null, wcmModuleURL('business/editorial/event/editor'));
	
	$tabs->addTab('Infos', 'Infos', false, null, wcmModuleURL('business/editorial/event/infos'));
    
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/event/properties'));    
	$tabs->addTab('_BIZ_SCHEDULES', _BIZ_SCHEDULES, false, null, wcmModuleURL('business/editorial/event/schedule'));
	
	$tabs->addTab('_BIZ_CATEGORIZATION', _BIZ_CATEGORIZATION, false, null, wcmModuleURL('business/shared/categorization'));
	$tabs->addTab('_RLX_SPECIALFOLDERS', _RLX_SPECIALFOLDERS, false, null, wcmModuleURL('business/shared/specialfolders'));
	$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media'));

    //$tabs->addTab('_BIZ_REFERENCING', _BIZ_REFERENCING, false, null, wcmModuleURL('business/shared/referencing'));
    $tabs->addTab('_BIZ_TME', _BIZ_TME, false, null, wcmModuleURL('business/tme/footprint'));
	
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
