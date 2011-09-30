<?php 
/**
 * Project:     WCM
 * File:        account_edit.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */
 
require_once dirname(__FILE__).'/../../../initWebApp.php';

$config = wcmConfig::getInstance();

$countryCode = getArrayParameter($_REQUEST, "kind", 0);

$url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylistforlocations.php';
$countryCode = "countryCode=".$countryCode;
$acOptions = array('url'=>$url, 'paramName'=>'prefix', 'minChars'=>'1', 'parameters'=>'max=15', 'parameters'=>$countryCode);
wcmGUI::renderAutoCompletedField($url, 'tempcity', null, _BIZ_LOCATION_CITY." ", array('class'=>'type-req', 'style'=>'width:400px'), $acOptions);

echo "<br>&nbsp;<br>&nbsp;";

echo "<ul class='toolbar'>";
echo "<li><a href='#' onclick=\"closemodal();\" class='save'>"._BIZ_OK."</a>&nbsp;&nbsp;&nbsp;&nbsp;</li>";
echo "</ul>";


echo "<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;";