<?php

/**
 * Project:     WCM
 * File:        slideshow.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

    // Execute action
    wcmMVC_Action::execute('business/slideshow', array('class' => 'slideshow'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
	$session = wcmSession::getInstance();
    
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
    $_SESSION['wcmActionMain'] = $_SESSION['wcmAction'];
    
    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    wcmGUI::renderObjectMenu();

	if (isset($bizobject->id) && !empty($bizobject->id))
    {
    	echo '<div id="treeview">';
	    $tabs = new wcmAjaxTabs('Informations', true);
	    if (!empty($bizobject->sourceVersion))
	    	$tabs->addTab('Actions', 'Actions', false, '<br /><br /><a class="list-builder" href="javascript:openDialog(\'business/pages/overview.php\',\'768\',\'1024\',\'768\',\'1024\',\'\');"><span><span>Overview</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="javascript:openDialog(\'business/pages/duplication.php\',\'400\',\'600\',\'400\',\'600\',\'\');"><span><span>Duplication</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="index.php?_wcmAction=business/news&id='.$bizobject->sourceVersion.'"><span><span>Go to associated News</span></span></a>');	
	    else 
	    	$tabs->addTab('Actions', 'Actions', false, '<br /><br /><a class="list-builder" href="javascript:openDialog(\'business/pages/overview.php\',\'768\',\'1024\',\'768\',\'1024\',\'\');"><span><span>Overview</span></span></a><br />&nbsp;<br />&nbsp;<br /><a class="list-builder" href="javascript:openDialog(\'business/pages/duplication.php\',\'400\',\'600\',\'400\',\'600\',\'\');"><span><span>Duplication</span></span></a><br />&nbsp;<br />');	
	    
	    $tabs->render();
    	echo '</div>';
    }
	
	//$tabs = new wcmAjaxTabs('navigation', true);
    //$tabs->addTab('tree', _SEARCH, true, wcmGUI::renderQuickSearchBox('className:'.$bizobject->getClass()));
    //$tabs->addTab('browse', _BROWSE, true, wcmGUI::renderBrowsePanel());
    //$tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    //$tabs->render();

    echo '<div id="content">';
    wcmGUI::openObjectForm($bizobject);

    $tabs = new wcmAjaxTabs('slideshow', true);
    //$tabs->addTab('_BIZ_OVERVIEW', _BIZ_OVERVIEW, false, null, wcmModuleURL('business/shared/overview'));
    $tabs->addTab('_BIZ_CONTENT', _BIZ_CONTENT, true, null, wcmModuleURL('business/editorial/slideshow/editor'));
    
    // case BIPH and Bang universe
    if ($session->getSiteId() == 11 || $session->getSiteId() == 12)
    	$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media', array('allowedUniverse' => array("11","12"))));
    elseif ($session->getSiteId() == 13 || $session->getSiteId() == 14)
		$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media', array('allowedUniverse' => array("13","14"))));
    else
		$tabs->addTab('_BIZ_MEDIA', _BIZ_MEDIA, false, null, wcmModuleURL('business/shared/media', array('allowedUniverse' => array("4","5","6"))));
	
	$tabs->addTab('_BIZ_PROPERTIES', _BIZ_PROPERTIES, false, null, wcmModuleURL('business/editorial/slideshow/properties'));
	$tabs->addTab('_BIZ_CATEGORIZATION', _BIZ_CATEGORIZATION, false, null, wcmModuleURL('business/shared/categorization'));
	$tabs->addTab('_RLX_SPECIALFOLDERS', _RLX_SPECIALFOLDERS, false, null, wcmModuleURL('business/shared/specialfolders'));
    //$tabs->addTab('_BIZ_REFERENCING', _BIZ_REFERENCING, false, null, wcmModuleURL('business/shared/referencing'));
    $tabs->addTab('_BIZ_TME', _BIZ_TME, false, null, wcmModuleURL('business/tme/footprint'));

    //Do not display those tabs for the moment
    /*$tabs->addTab('_BIZ_DESIGN', _BIZ_DESIGN, false, null, wcmModuleURL('business/shared/design'));
     */

    $tabs->render($bizobject->id === 0);

    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
?>
