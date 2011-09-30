<?php
/**
 * Project:     WCM
 * File:        modules/editorial/location/infos.php
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
$session = wcmSession::getInstance();
$mySite = $session->getSite();
$lang = $mySite->language;

wcmGUI::openFieldset('');
wcmGUI::renderHiddenField('countryId', $bizobject->countryId);
wcmGUI::renderHiddenField('countryCode', $bizobject->getCountryCodeById());
wcmGUI::renderHiddenField('cityId', $bizobject->cityId);
wcmGUI::renderTextField('title', $bizobject->title, _BIZ_LOCATION_NAME);
wcmGUI::renderTextArea('address_1', $bizobject->address_1, _BIZ_LOCATION_ADDRESS_1);
wcmGUI::renderTextArea('address_2', $bizobject->address_2, _BIZ_LOCATION_ADDRESS_2);
// *** Country
$url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylistforlocations.php';
$acOptions = array('url'=>$url, 'paramName'=>'prefix', 'minChars'=>'1', 'parameters'=>'max=15');
wcmGUI::renderAutoCompletedField($url, 'country', $bizobject->country, _BIZ_LOCATION_COUNTRY, array('class'=>'type-req'), $acOptions);
// *** ZipCode
wcmGUI::renderTextField('zipcode', $bizobject->zipcode, _BIZ_LOCATION_ZIPCODE);
// *** City
/*
$url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylistforlocations.php';
$countryCode = "countryCode=".$bizobject->getCountryCodeById();
$acOptions = array('url'=>$url, 'paramName'=>'prefix', 'minChars'=>'1', 'parameters'=>'max=15', 'parameters'=>$countryCode);
wcmGUI::renderAutoCompletedField($url, 'city', $bizobject->city, _BIZ_LOCATION_CITY, array('class'=>'type-req', 'style'=>'width:400px'), $acOptions);
*/
wcmGUI::renderTextField('city', $bizobject->city, _BIZ_LOCATION_CITY, array('readonly' => 'readonly', 'onclick'=>'cityUpdate(document.getElementById(\'countryCode\').value);'));
// *** Infos
wcmGUI::renderTextField('phone', $bizobject->phone, _BIZ_LOCATION_PHONE);
wcmGUI::renderTextField('email', $bizobject->email, _BIZ_LOCATION_EMAIL);
wcmGUI::renderTextField('website', $bizobject->website, _BIZ_LOCATION_WEBSITE);

wcmGUI::closeFieldset();
