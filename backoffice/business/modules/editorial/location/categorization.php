<?php
/**
 * Project:     WCM
 * File:        modules/editorial/location/categorization.php
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

    $_SESSION['wcm']['footprint']['context'] = $bizobject;


    echo '<div class="zone">';

	echo '<div style="display:none;">';
		/*
	     * Display Channels
	     */
		wcmGUI::openCollapsablePane(_BIZ_CATEGORIZATION_CHANNELS_CHOICE);
		$channel = new channel();
	    $channelsHtml = $channel->getChannelsHtml($bizobject);
	    echo $channelsHtml;
	    wcmGUI::closeCollapsablePane();
		echo '<div style="display:block; clear:both; height:2em;"></div>';
	echo '</div>';

	/*
     * Display Lists
     */
    wcmGUI::openCollapsablePane(_BIZ_CATEGORIZATION_LISTS_CHOICE);
	$listHtml = getListHtml($bizobject);
    echo $listHtml;
    wcmGUI::closeCollapsablePane();
    echo '<div style="display:block; clear:both; height:2em;"></div>';



    wcmGUI::openCollapsablePane(_BIZ_TAXONOMIES);

    // @todo :: TME automatic IPTC categorization
    wcmGUI::openFieldset(_BIZ_CATEGORIZATION_IPTC);
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/taxonomy/biz.autocomplete.php',
                       'paramName' => 'prefix',
                       'parameters' => 'type=iptc');
    wcmGUI::renderListField('_semanticData[categories]', array_keys($bizobject->semanticData->categories), null, $acOptions, _BIZ_CATEGORIZATION_IPTC);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

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
