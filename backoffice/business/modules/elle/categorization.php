<?php
/**
 * Project:     WCM
 * File:        modules/elle/categorization.php
 *
 * @copyright   (c)2011 Nstein Technologies
 * @version     4.x
 *
 */
    require_once(WCM_DIR.'/business/api/toolbox/biz.relax.toolbox.php');

    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
	$session = wcmSession::getInstance();
	$currentSite = $session->getSite();
		
    $_SESSION['wcm']['footprint']['context'] = $bizobject;


    //if (!empty($bizobject->id) && empty($bizobject->channelId))
    //	echo "<div style=\"text-align:center;color:red;font-weight: bold;font-size:larger;\">"._BIZ_ERROR_CHANNELID_IS_MANDATORY."<br /><br /></div>";
    	
    echo '<div class="zone">';
	
     wcmGUI::openCollapsablePane("THEME");
		$listHtml = getListHtml($bizobject, 600);
	    echo $listHtml;
	    wcmGUI::closeCollapsablePane();
    
	echo '<div style="display:block; clear:both; height:2em;"></div>';
    /*
     * Display Channels
     */
    wcmGUI::openCollapsablePane("CATEGORISATION");

	$channel = new channel();
	$channelsHtml = $channel->getLinearChannelsHtml($bizobject, $bizobject->siteId);
	echo $channelsHtml;
	
	wcmGUI::closeCollapsablePane();

    echo '</div>';
