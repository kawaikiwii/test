<?php
/**
 * Project:     WCM
 * File:        modules/elle/properties.php
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

	wcmGUI::openCollapsablePane("CATEGORISATION");
	wcmGUI::openFieldset();
	wcmGUI::renderDropdownField('theme', $bizobject->getThemes(), $bizobject->theme, 'THEME');
    wcmGUI::renderDropdownField('channelId', $bizobject->getChannels(), $bizobject->channelId, 'RUBRIQUE');
    wcmGUI::renderDropdownField('zone_print', $bizobject->getZones(), $bizobject->zone_print, 'ZONE PRINT');
    wcmGUI::renderBooleanField('focus', $bizobject->focus, "FOCUS");
    wcmGUI::closeFieldset();
	wcmGUI::closeCollapsablePane();
    
	
    wcmGUI::openCollapsablePane(_INFORMATIONS);
    wcmGUI::openFieldset();
    
	wcmGUI::renderTextField('placeTitle', 	$bizobject->placeTitle, _BIZ_TITLE);
    wcmGUI::renderTextArea('text', 		$bizobject->text, 		_BIZ_LOCATION_COMMENTS, array('rows'=>5));
    
    echo "<br />&nbsp;<br />";   
    wcmGUI::renderTextField('title', 	$bizobject->title, 		_BIZ_PLACE);
    //wcmGUI::renderTextArea('address', 	$bizobject->address, 	_BIZ_ADDRESS, array('rows'=>3));
	wcmGUI::renderTextField('address', 	$bizobject->address, 	_BIZ_ADDRESS);
	//wcmGUI::renderHiddenField('cityId', $bizobject->cityId);
    //wcmGUI::renderTextField('city', 	$bizobject->city, 		_BIZ_CITY);
    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylistElleWS.php';
	$acOptions = array('url'=>$url, 'paramName'=>'prefix', 'minChars'=>'1', 'parameters'=>'max=12');
	wcmGUI::renderAutoCompletedField($url, 'city', $bizobject->city, _BIZ_LOCATION_CITY, array('class'=>'type-req', 'style'=>'font-weight:bold;background-color:PaleTurquoise;'), $acOptions);
	
    wcmGUI::renderTextField('zipcode', 	$bizobject->zipcode, 	_BIZ_ZIPCODE);
	wcmGUI::renderTextField('region', 	$bizobject->getRegionLabel($bizobject->region), 	_BIZ_REGION);
    wcmGUI::renderTextField('country', 	$bizobject->country, 	_BIZ_COUNTRY);
    
    // add google map interface
    relaxGUI::renderGoogleMap($bizobject);
    /*
    echo '<iframe src="'.$config['wcm.backOffice.url'].'business/modules/shared/googleMap.php?lat='.$bizobject->latitude.'&lon='.$bizobject->longitude.'" name="gmap" height="440" width="800"></iframe>';  
    wcmGUI::renderHiddenField('latitude', $bizobject->latitude);
	wcmGUI::renderHiddenField('longitude', $bizobject->longitude);
	*/
     
    echo "<br />&nbsp;<br />";     
    wcmGUI::renderTextField('phone', 	$bizobject->phone, 		_BIZ_PHONE);
    wcmGUI::renderTextField('email', 	$bizobject->email, 		_BIZ_EMAIL, array('class' => 'type-email'));
    wcmGUI::renderTextField('website', 	$bizobject->website, 	_BIZ_WEBSITE."  ( http:// )"); 
    wcmGUI::renderTextArea('opening', 	$bizobject->opening, 	_BIZ_OPENING, array('rows'=>3));
	wcmGUI::renderTextField('price', 	$bizobject->price, 		_BIZ_LOCATION_PRICE);
           
    /*
	$url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.placeEllePeople.php';
	$acOptions = array('url'=>$url, 'paramName'=>'prefix', 'minChars'=>'1', 'parameters'=>'max=12');
	wcmGUI::renderAutoCompletedField($url, 'people', $bizobject->people, _BIZ_PEOPLE, array('style'=>'font-weight:bold;background-color:PaleTurquoise;'), $acOptions);
    */
	
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    wcmGUI::openCollapsablePane("PEOPLE");
    wcmGUI::openFieldset();
    
    echo '<div id="divpeople">';
	wcmGUI::openFieldset("");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.placeEllePeople.php',
                       'paramName' => 'prefix', 'minChars'=>'1', 'parameters' => 'max=12', 'tokens' => ',');
    wcmGUI::renderCommonListField('people', $bizobject->people, null, $acOptions);
	echo '</div>';
	wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    echo '</div>';