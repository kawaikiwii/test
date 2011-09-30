<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/properties.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    
    echo '<div class="zone">';
    
    wcmGUI::openCollapsablePane("Informations générales");
    wcmGUI::openFieldset("Identification");
    
    echo '<div id="echoCity_cityfields'.uniqid().'">';
	wcmGUI::openFieldset("Ville");
	$hCityGeoNameId = 'cityGeoNameId'.uniqid();
	wcmGUI::renderHiddenField('cityGeoNameId', $bizobject->cityGeoNameId, array('id' => $hCityGeoNameId));
    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
    wcmGUI::renderCommonListField2('title', $bizobject->title, null, $acOptions, uniqid());
	wcmGUI::closeFieldset();
	echo '</div>';
	
    echo '<div id="echoCity_countryfields'.uniqid().'">';
	wcmGUI::openFieldset("Pays");
    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
	wcmGUI::renderHiddenField('countryGeoNameId', $bizobject->countryGeoNameId, array('id' => $hCountryGeoNameId));
    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
    wcmGUI::renderCommonListField2('country', $bizobject->country, null, $acOptions, uniqid());
	wcmGUI::closeFieldset();
	echo '</div>';
		
    if (!empty($bizobject->latitude) && !empty($bizobject->longitude))
	{
		$position = urlencode($bizobject->latitude." ".$bizobject->longitude);
		$url = "http://maps.google.fr/maps?q=".$position;
		echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
	}
	
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    wcmGUI::openCollapsablePane("Informations pratiques");
    wcmGUI::openFieldset("Commun");
    wcmGUI::renderTextArea('description', 			$bizobject->description, 			"Description");
    wcmGUI::renderTextArea('populationDescription', $bizobject->populationDescription, 	"Population (description)");
    wcmGUI::renderTextField('populationNumber', 	$bizobject->populationNumber, 		"Population (nombre)");
    wcmGUI::renderTextField('populationDensity', 	$bizobject->populationDensity, 		"Population (densité)");  
    wcmGUI::renderTextArea('climateDescription', 	$bizobject->climateDescription, 	"Climat (description)");
    wcmGUI::renderTextField('climateTemperatureWinter', $bizobject->climateTemperatureWinter, 	"Temp. hiver (°C)");  
    wcmGUI::renderTextField('climateTemperatureSummer', $bizobject->climateTemperatureSummer, 	"Temp. été   (°C)");  
    wcmGUI::renderTextField('visa', 				$bizobject->visa, 					"Visa");
    wcmGUI::renderTextArea('languagesDescription', 	$bizobject->languagesDescription, 	"Langues (description)");
    wcmGUI::renderTextArea('officialLanguages', 	$bizobject->officialLanguages, 		"Langue(s) officielle(s)");
    
    wcmGUI::renderTextArea('jetlagDescription', 	$bizobject->jetlagDescription, 		"Décalage horaire (description)");
    wcmGUI::renderTextField('jetlagGmt', 			$bizobject->jetlagGmt, 				"Décalage horaire GMT (h)");
    wcmGUI::renderTextField('web', 					$bizobject->web, 					"Web");
    wcmGUI::closeFieldset();
    
    wcmGUI::openFieldset("Cartes");
	    wcmGUI::openFieldset("Ville");
	    wcmGUI::renderTextField('mapCityCaption', 	$bizobject->mapCityCaption, 		"Titre");
	    wcmGUI::renderTextField('mapCityCredits', 	$bizobject->mapCityCredits, 		"Crédits");
	    //wcmGUI::renderTextField('mapCityFile', 	$bizobject->mapCityFile, 			"Fichier");
	    
	    wcmGUI::renderFileField('mapCityFile', 	"Fichier", array("id"=>"mapCityFile"));
	    if (!empty($bizobject->mapCityFile)) 	echo "<a href='".$config['wcm.backOffice.url'].$config['wcm.backOffice.photosPathLesEchos'].DIRECTORY_SEPARATOR.$bizobject->mapCityFile."' target='_blank'>cliquez ici pour voir l'image :  ".$bizobject->mapCityFile."</a>";
	    
	    wcmGUI::closeFieldset();
	    
	    wcmGUI::openFieldset("Transport");
	    wcmGUI::renderTextField('mapTransportCaption', 	$bizobject->mapTransportCaption, 	"Titre");
	    wcmGUI::renderTextField('mapTransportCredits', 	$bizobject->mapTransportCredits, 	"Crédits");
	    //wcmGUI::renderTextField('mapTransportFile', 	$bizobject->mapTransportFile, 		"Fichier");
	    
	    wcmGUI::renderFileField('mapTransportFile', "Fichier", array("id"=>"mapTransportFile"));
	    if (!empty($bizobject->mapTransportFile)) 	echo "<a href='".$config['wcm.backOffice.url'].$config['wcm.backOffice.photosPathLesEchos'].DIRECTORY_SEPARATOR.$bizobject->mapTransportFile."' target='_blank'>cliquez ici pour voir l'image :  ".$bizobject->mapTransportFile."</a>";
	    
	    wcmGUI::closeFieldset();
	    
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    echo '</div>';
