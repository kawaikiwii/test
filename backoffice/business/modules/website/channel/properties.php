<?php

/**
 * Project:     WCM
 * File:        modules/channel/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
	$channels = array('0' => _BIZ_ROOT_ELEMENT );
	$config = wcmConfig::getInstance();
	
	$bizobject->getChannelHierarchy($channels, $bizobject->id);

    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_META_CONTENT);

    wcmGUI::openCollapsableFieldset(_GENERAL);
    wcmGUI::renderDropdownField('parentId', $channels, $bizobject->parentId, _BIZ_CHANNEL);
	wcmGUI::renderTextField('title', $bizobject->title, _BIZ_TITLE . ' *', array('class' => 'type-req'));
	wcmGUI::renderTextField('rank', $bizobject->rank, _BIZ_POSITION, array('class' => 'type-int'));
	wcmGUI::renderTextField('css', $bizobject->css, _BIZ_CSS);
	wcmGUI::renderTextArea('description', $bizobject->description, _BIZ_DESCRIPTION);
	
	wcmGUI::renderTextArea('tokens', $bizobject->tokens, "Tokens");
	
	//$getChilds = channel::getArrayChannelFinalChilds(0, 6);
	//print_r($getChilds);
	/*
	$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($getChilds));
	foreach ($it as $key=>$val)
	{
		if (!empty($val)) 
		{
			if ($key == "title") echo "<br /><br />";
			echo $key.":".$val."<br />\n";
		}
	}
	*/
	wcmGUI::closeCollapsablePane();
	
	wcmGUI::openCollapsablePane(_BIZ_TAXONOMIES);

	//print_r(channel::getChannelIPTC());
	// @todo :: TME automatic IPTC categorization
    echo '<div id="iptcfields">';
	wcmGUI::openFieldset(_BIZ_CATEGORIZATION_IPTC);
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.iptcchannel.php',
                       'paramName' => 'prefix', 'parameters' => 'type=iptc');
    wcmGUI::renderSpecialListField('iptc', $bizobject->iptc, null, $acOptions);
	wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
	echo '</div>';
	
	wcmGUI::closeFieldset();    
    
    /*wcmGUI::openCollapsablePane(_PUBLICATION_GENERATION);
    wcmGUI::openFieldset( _LIFETIME);
    wcmGUI::renderDateField('publicationDate',_BIZ_PUBLICATIONDATE,_BIZ_PUBLICATIONDATE);
    wcmGUI::renderDateField('expirationDate', _BIZ_EXPIRATIONDATE,_BIZ_EXPIRATIONDATE);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    */
    echo '</div>';
