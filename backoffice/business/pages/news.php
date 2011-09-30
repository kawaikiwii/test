<?php
/**
 * Project:     WCM
 * File:        news.php
 *
 * @copyright   (c)2008 Relaxnews
 * @version     4.x
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/news', array('class' => 'news'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
	$session = wcmSession::getInstance();
	
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
    $_SESSION['wcmActionMain'] = $_SESSION['wcmAction'];
    
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

   // echo '<div id="treeview">';
    //$siteId = $_SESSION['wcmSession']->getSiteId();
    //$tabs = new wcmAjaxTabs('navigation', true);
    //$tabs->addTab('tree', _SEARCH, true, wcmGUI::renderQuickSearchBox('className:'.$bizobject->getClass()));
    //$tabs->addTab('browse', _BROWSE, true, wcmGUI::renderBrowseObject($bizobject->getClass(), "workflowState = 'draft' AND siteId='$siteId' LIMIT 30"));
    //$tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    // @todo :: Google keywords API panel
    //$tabs->render();
    //echo '</div>';
    
    if (isset($bizobject->id) && !empty($bizobject->id))
    {
    	echo '<div id="treeview">';
	    $tabs = new wcmAjaxTabs('Informations', true);
	    
	    $slideshow = new slideshow();
	    $slideshow->refreshBySourceVersion($bizobject->id);
	    
	    if (empty($slideshow->id))
	    	$tabs->addTab('Actions', 'Actions', false, '<br /><br /><a class="list-builder" href="javascript:openDialog(\'business/pages/overview.php\',\'768\',\'1024\',\'768\',\'1024\',\'\');"><span><span>Overview</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="javascript:openDialog(\'business/pages/duplication.php\',\'400\',\'600\',\'400\',\'600\',\'\');"><span><span>Duplication</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="javascript:wcmActionController.triggerEvent(\'createslideshow\',{});"><span><span>Create Slideshow</span></span></a>');	
		else 
			$tabs->addTab('Actions', 'Actions', false, '<br /><br /><a class="list-builder" href="javascript:openDialog(\'business/pages/overview.php\',\'768\',\'1024\',\'768\',\'1024\',\'\');"><span><span>Overview</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="javascript:openDialog(\'business/pages/duplication.php\',\'400\',\'600\',\'400\',\'600\',\'\');"><span><span>Duplication</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="index.php?_wcmAction=business/slideshow&id='.$slideshow->id.'"><span><span>Go to associated Slideshow</span></span></a>');	
		
	    $tabs->render();
    	echo '</div>';
    }

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);
    $tabs = new wcmAjaxTabs('news', true);
    //$tabs->addTab('_BIZ_OVERVIEW', _BIZ_OVERVIEW, false, null, wcmModuleURL('business/shared/overview'));
    $tabs->addTab('_BIZ_CONTENT', _BIZ_CONTENT, true, null, wcmModuleURL('business/editorial/news/editor'));
    
    // example : limit photo acccess to current universe
    //$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media', array('onlyUniverse' => true))));
    
    // case BIPH and Bang universe
    if ($session->getSiteId() == 11 || $session->getSiteId() == 12)
    	$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media', array('allowedUniverse' => array("11","12"))));
    elseif ($session->getSiteId() == 13 || $session->getSiteId() == 14)
		$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media', array('allowedUniverse' => array("13","14"))));
    else
		$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media', array('allowedUniverse' => array("4","5","6"))));
		
    $tabs->addTab('_BIZ_VIDEO', _BIZ_VIDEO, false, null, wcmModuleURL('business/editorial/news/video', array('onlyUniverse' => true)));
    $tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/news/properties'));
	$tabs->addTab('_BIZ_CATEGORIZATION', _BIZ_CATEGORIZATION, false, null, wcmModuleURL('business/shared/categorization'));
	
	$tabs->addTab('_RLX_SPECIALFOLDERS', _RLX_SPECIALFOLDERS, false, null, wcmModuleURL('business/shared/specialfolders'));

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
