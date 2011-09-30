<?php
/**
 * Project:     WCM
 * File:        modules/shared/categorization.php
 *
 * @copyright   (c)2008 Nstein Technologies
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


    if (!empty($bizobject->id) && empty($bizobject->channelId) && ($bizobject->getClass() != "prevision"))
    	echo "<div style=\"text-align:center;color:red;font-weight: bold;font-size:larger;\">"._BIZ_ERROR_CHANNELID_IS_MANDATORY."<br /><br /></div>";
    	
    echo '<div class="zone">';
	
    /*
     * Display Channels
     */
    wcmGUI::openCollapsablePane(_BIZ_CATEGORIZATION_CHANNELS_CHOICE);
	wcmGUI::renderHiddenField('tab_categorization', "1");
	
	// We change language to display correct Channels
	//$session = wcmSession::getInstance();
	//$originalLanguage = $session->getLanguage();
	
	//$session->setLanguage($session->getSite()->language);
	//$session->refresh();echo $session->getLanguage();echo getConst('_BIZ_WCM_LANGUAGE');
	$channel = new channel();
	$channelsHtml = $channel->getChannelsHtml($bizobject, $bizobject->siteId);
	echo $channelsHtml;
	
	//$session->setLanguage($originalLanguage);
	//$session->refresh();

	wcmGUI::closeCollapsablePane();
	echo '<div style="display:block; clear:both; height:2em;"></div>';

	echo '<div style="display:block; clear:both; height:2em;"></div>';

	wcmGUI::openCollapsablePane(_BIZ_TAXONOMIES);
    echo '<div id="iptcfields">';
	wcmGUI::openFieldset(_BIZ_CATEGORIZATION_IPTC);
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.iptcchannel.php',
                       'paramName' => 'prefix', 'parameters' => 'type=iptc');
    wcmGUI::renderSpecialListField('iptc', $bizobject->iptc, null, $acOptions);
	wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
	echo '</div>';
	
	/*
     * Display Lists except for biph universe
     */
	if ($currentSite->code != "bfr" && $currentSite->code != "ben")
	{
	    wcmGUI::openCollapsablePane(_BIZ_CATEGORIZATION_LISTS_CHOICE);
		$listHtml = getListHtml($bizobject);
	    echo $listHtml;
	    wcmGUI::closeCollapsablePane();
	}
    
	/*
    wcmGUI::openCollapsablePane(_BIZ_TAXONOMIES);

    // @todo :: TME automatic IPTC categorization
    echo '<div id="iptcfields">';
	wcmGUI::openFieldset(_BIZ_CATEGORIZATION_IPTC);
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/taxonomy/biz.autocomplete.php',
                       'paramName' => 'prefix',
                       'parameters' => 'type=iptc');
    wcmGUI::renderListField('_semanticData[categories]', array_keys($bizobject->semanticData->categories), null, $acOptions, _BIZ_CATEGORIZATION_IPTC);
	wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
	echo '</div>';
	*/
    wcmGUI::openCollapsablePane(_BIZ_CATEGORIZATION_TAGS);
    wcmGUI::openFieldset(_BIZ_TAGS);
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/taxonomy/biz.autocomplete.php',
                       'paramName' => 'prefix',
                       'parameters' => 'type=general');
    wcmGUI::renderListField('_xmlTags[tags]', getArrayParameter($bizobject->xmlTags, 'tags'), null, $acOptions);
    wcmGUI::closeFieldset();

    /*wcmGUI::openFieldset(_BIZ_AD_SERVER);
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/taxonomy/biz.autocomplete.php',
                       'paramName' => 'prefix',
                       'parameters' => 'type=ads');
    wcmGUI::renderListField('_xmlTags[ads]', getArrayParameter($bizobject->xmlTags, 'ads'), null, $acOptions);
    wcmGUI::closeFieldset();*/

    wcmGUI::closeCollapsablePane();

    echo '</div>';
