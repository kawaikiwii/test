<?php
/**
 * Project:     WCM
 * File:        modules/editorial/place/properties.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
    
	/* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
    
	echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_INFORMATIONS);
    wcmGUI::openFieldset();
    
	wcmGUI::renderTextField('title', 	$bizobject->title, 		_BIZ_PLACE);
    wcmGUI::renderTextArea('address', 	$bizobject->address, 	_BIZ_ADDRESS, array('rows'=>3));
	wcmGUI::renderTextField('zipcode', 	$bizobject->zipcode, 	_BIZ_ZIPCODE);
    wcmGUI::renderTextField('city', 	$bizobject->city, 		_BIZ_CITY);
    wcmGUI::renderTextField('country', 	$bizobject->country, 	_BIZ_COUNTRY);
    wcmGUI::renderTextField('phone', 	$bizobject->phone, 		_BIZ_PHONE);
    wcmGUI::renderTextField('email', 	$bizobject->email, 		_BIZ_EMAIL);
    wcmGUI::renderTextField('website', 	$bizobject->website, 	_BIZ_WEBSITE);
    wcmGUI::renderTextField('facebook', $bizobject->facebook, 	"Facebook");
    
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
	
    echo '</div>';