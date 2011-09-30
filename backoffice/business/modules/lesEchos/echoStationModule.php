<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoStationModule.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoStation_text_');
    $echoStation = getArrayParameter($params, 'echoStation', new echoStation());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoStationModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoStation'
			);

    $info = '<ul>';
    foreach ($menus as $title => $action)
    {
    	if ($title == getConst(_DELETE))
    	{
           $info .= '<li><a href="#" onclick="'.$action.'(this, \''.$uniqid.'\'); return false;">' . $title . '</a></li>';
    	}
    	else
    	{        
    	   $info .= '<li><a href="#" onclick="addEchoStation(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoStation->title)) ? "Gare" . ' ' . $echoStation->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoStation" style="clear: both;">';
		wcmGUI::renderHiddenField('echoStation_id[]', $echoStation->id, array('id' => uniqid()));
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoStationFieldset'. $echoStation->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderTextField('echoStation_title[]',		$echoStation->title, 		"Nom");
		wcmGUI::renderTextArea('echoStation_description[]', $echoStation->description, 	"Description");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextArea('echoStation_address[]', 	$echoStation->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoStation_postalCode[]',	$echoStation->postalCode, 	"Code postal");
		wcmGUI::renderTextArea('echoStation_transportation[]',$echoStation->transportation, "Transport");
		
		echo '<div id="echoStation_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoStation_cityGeoNameId[]', $echoStation->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoStation_city[]', $echoStation->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoStation_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoStation_countryGeoNameId[]', $echoStation->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoStation_country[]', $echoStation->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoStation->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoStation_longitude[]', $echoStation->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoStation_latitude[]', $echoStation->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoStation_autogmap[]', $echoStation->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	    
	    if (!empty($echoStation->latitude) && !empty($echoStation->longitude))
		{
			$position = urlencode($echoStation->latitude." ".$echoStation->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoStation_phone[]', 	$echoStation->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoStation_email[]',		$echoStation->email, 		"Email");
		wcmGUI::renderTextField('echoStation_web[]',		$echoStation->web, 		"Web");
		wcmGUI::closeFieldset();
        
		wcmGUI::openFieldset("Carte");
		wcmGUI::renderTextField('echoStation_mapTitle[]', 	$echoStation->mapTitle, 	"Titre");	
		wcmGUI::renderTextField('echoStation_mapCredits[]',	$echoStation->mapCredits, 	"Crédits");
		//wcmGUI::renderTextField('echoStation_mapFile[]',	$echoStation->mapFile, 		"Fichier");
		
		$idForPicture = 'stationMapFile'.uniqid();
		wcmGUI::renderHiddenField('echoStation_mapFile2[]', $echoStation->mapFile, array('id' => $idForPicture.'2'));
	    wcmGUI::renderFileField('echoStation_mapFile[]', 			"Fichier", array("id"=>$idForPicture));
	    if (!empty($echoStation->mapFile)) echo "<a href='".$config['wcm.backOffice.url'].$config['wcm.backOffice.photosPathLesEchos'].DIRECTORY_SEPARATOR.$echoStation->mapFile."' target='_blank'>cliquez ici pour voir l'image :  ".$echoStation->mapFile."</a>";
	    
		wcmGUI::closeFieldset();
		
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";