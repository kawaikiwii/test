<?php
/**
 * Project:     WCM
 * File:        modules/editorial/organisation/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();

    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_SOURCE_INFORMATION);

	wcmGUI::openFieldset('');   
	wcmGUI::renderTextField('company', 		$bizobject->company, 		_BIZ_COMPANYNAME);
	wcmGUI::renderTextField('name', 		$bizobject->name, 		_BIZ_NAME);
	//wcmGUI::renderDateField('founded', 		$bizobject->founded, 	_BIZ_LOCATION_FOUNDED, 'date');
	//wcmGUI::renderTextField('nationality', 	$bizobject->nationality,_BIZ_LOCATION_NATIONALITY);
	//wcmGUI::renderTextField('sector', 		$bizobject->sector, 	_BIZ_LOCATION_SECTOR);
	wcmGUI::renderDropdownField('type', 	$bizobject->getTypeList(), $bizobject->type, _BIZ_LOCATION_TYPE);
	//wcmGUI::renderDropdownField('sector', 	$bizobject->getSectorList(), $bizobject->sector, _BIZ_LOCATION_SECTOR);
	wcmGUI::openFieldset(_BIZ_LOCATION_SECTOR);   
	relaxGUI::getArrayColumnsCheckboxes('sector', 	$bizobject->getSectorList(), unserialize($bizobject->sector));
	wcmGUI::closeFieldset();
	wcmGUI::renderDropdownField('service', 	$bizobject->getServiceList(), $bizobject->service, _BIZ_LOCATION_SERVICE);
	wcmGUI::renderTextArea('comments', 		$bizobject->comments, 	_BIZ_LOCATION_COMMENTS);
	wcmGUI::renderTextArea('clients', 		$bizobject->clients, 	_BIZ_LOCATION_CLIENTS);
	wcmGUI::closeFieldset();
    
	wcmGUI::openFieldset('');   
	wcmGUI::renderTextField('address_1', 	$bizobject->address_1, 	_BIZ_LOCATION_ADDRESS_1);
	wcmGUI::renderTextField('address_2', 	$bizobject->address_2, 	_BIZ_LOCATION_ADDRESS_2);
    wcmGUI::renderTextField('zipcode', 		$bizobject->zipcode, 	_BIZ_LOCATION_ZIPCODE);
	wcmGUI::renderTextField('city', 		$bizobject->city, 		_BIZ_LOCATION_CITY);
    wcmGUI::renderTextField('country', 		$bizobject->country, 	_BIZ_LOCATION_COUNTRY);	
	wcmGUI::renderTextField('phone', 		$bizobject->phone, 		_BIZ_LOCATION_PHONE);
	wcmGUI::renderTextField('mobile', 		$bizobject->mobile, 	_BIZ_LOCATION_MOBILE);
	wcmGUI::renderTextField('fax', 			$bizobject->fax, 		_BIZ_LOCATION_FAX);
	wcmGUI::renderTextField('email', 		$bizobject->email, 		_BIZ_LOCATION_EMAIL);
	wcmGUI::closeFieldset();
	
    wcmGUI::openFieldset('');   
	wcmGUI::renderTextField('website', 		$bizobject->website, 	_BIZ_LOCATION_WEBSITE);
	wcmGUI::renderTextField('facebookUrl', 	$bizobject->facebookUrl,"Url Facebook");
	wcmGUI::renderTextField('twitterUrl', 	$bizobject->twitterUrl, "Url Twitter");
	wcmGUI::renderTextField('myspaceUrl', 	$bizobject->myspaceUrl, "Url Myspace");
	//wcmGUI::renderTextField('latitude', 	$bizobject->latitude, 	_BIZ_LOCATION_LATITUDE);
	//wcmGUI::renderTextField('longitude', 	$bizobject->longitude, 	_BIZ_LOCATION_LONGITUDE);
	wcmGUI::closeFieldset();   
	
    wcmGUI::closeCollapsablePane();

    echo '</div>';
