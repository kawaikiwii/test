<?php
/**
 * Project:     WCM
 * File:        modules/editorial/person/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
    
    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_SOURCE_INFORMATION);

	wcmGUI::openFieldset('');
    
	wcmGUI::renderDropdownField('civility', 	$bizobject->getCivility(), $bizobject->civility, 'Civility');
	wcmGUI::renderTextField('firstname', 		$bizobject->firstname, 	_BIZ_LOCATION_FIRSTNAME);
	wcmGUI::renderTextField('lastname', 		$bizobject->lastname, 	_BIZ_LOCATION_LASTNAME);
	wcmGUI::renderTextField('nickname', 		$bizobject->nickname, 	_BIZ_LOCATION_NICKNAME);
	wcmGUI::renderDateField('born', 			$bizobject->born, 		_BIZ_LOCATION_BORN, 'date');
	wcmGUI::renderDateField('deceaseDate', 		$bizobject->deceaseDate,_BIZ_LOCATION_DECEASEDATE, 'date');
	wcmGUI::renderDropdownField('nationality', 	$bizobject->getNationalityList(), $bizobject->nationality, "Nationality");
	wcmGUI::renderDropdownField('jobtitle', 	$bizobject->getJobtitleList(), $bizobject->jobtitle, "Job title");
	wcmGUI::renderTextField('company', 			$bizobject->company, 	_BIZ_LOCATION_COMPANY);
	wcmGUI::renderTextField('address_1', 		$bizobject->address_1, 	_BIZ_LOCATION_ADDRESS_1);
	wcmGUI::renderTextField('address_2', 		$bizobject->address_2, 	_BIZ_LOCATION_ADDRESS_2);
    wcmGUI::renderTextField('zipcode', 			$bizobject->zipcode, 	_BIZ_LOCATION_ZIPCODE);
	wcmGUI::renderTextField('city', 			$bizobject->city, 		_BIZ_LOCATION_CITY);
    wcmGUI::renderTextField('country', 			$bizobject->country, 	_BIZ_LOCATION_COUNTRY);	
	wcmGUI::renderTextField('phone', 			$bizobject->phone, 		_BIZ_LOCATION_PHONE);
	wcmGUI::renderTextField('mobile', 			$bizobject->mobile, 	_BIZ_LOCATION_MOBILE);
	wcmGUI::renderTextField('fax', 				$bizobject->fax, 		_BIZ_LOCATION_FAX);
	wcmGUI::renderTextField('email', 			$bizobject->email, 		_BIZ_LOCATION_EMAIL);
	wcmGUI::renderTextField('website', 			$bizobject->website, 	_BIZ_LOCATION_WEBSITE);
	wcmGUI::renderTextField('facebookUrl', 		$bizobject->facebookUrl,"Url Facebook");
	wcmGUI::renderTextField('twitterUrl', 		$bizobject->twitterUrl, "Url Twitter");
	wcmGUI::renderTextField('myspaceUrl', 		$bizobject->myspaceUrl, "Url Myspace");
	wcmGUI::renderTextField('latitude', 		$bizobject->latitude, 	_BIZ_LOCATION_LATITUDE);
	wcmGUI::renderTextField('longitude', 		$bizobject->longitude, 	_BIZ_LOCATION_LONGITUDE);
	wcmGUI::renderTextArea('comments', 			$bizobject->comments, 	_BIZ_LOCATION_COMMENTS);
	
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    echo '</div>';
