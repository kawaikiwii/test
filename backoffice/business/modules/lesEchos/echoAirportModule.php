<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoAirportModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoAirport_text_');
    $echoAirport = getArrayParameter($params, 'echoAirport', new echoAirport());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoAirportModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoAirport'
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
    	   $info .= '<li><a href="#" onclick="addEchoAirport(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoAirport->title)) ? "Aéroport" . ' ' . $echoAirport->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoAirport" style="clear: both;">';
		wcmGUI::renderHiddenField('echoAirport_id[]', $echoAirport->id, array('id' => uniqid()));
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoAirportFieldset'. $echoAirport->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderTextField('echoAirport_title[]',		$echoAirport->title, 		"Nom");
		wcmGUI::renderTextArea('echoAirport_description[]', $echoAirport->description, 	"Description");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextArea('echoAirport_address[]', 	$echoAirport->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoAirport_postalCode[]',	$echoAirport->postalCode, 	"Code postal");
		wcmGUI::renderTextArea('echoAirport_transportation[]',$echoAirport->transportation, "Transport");
		
		echo '<div id="echoAirport_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoAirport_cityGeoNameId[]', $echoAirport->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoAirport_city[]', $echoAirport->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoAirport_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoAirport_countryGeoNameId[]', $echoAirport->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoAirport_country[]', $echoAirport->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoAirport->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoAirport_longitude[]', $echoAirport->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoAirport_latitude[]', $echoAirport->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoAirport_autogmap[]', $echoAirport->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	    
	    if (!empty($echoAirport->latitude) && !empty($echoAirport->longitude))
		{
			$position = urlencode($echoAirport->latitude." ".$echoAirport->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoAirport_phone[]', 	$echoAirport->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoAirport_email[]',		$echoAirport->email, 		"Email");
		wcmGUI::renderTextField('echoAirport_web[]',		$echoAirport->web, 		"Web");
		wcmGUI::closeFieldset();
        
		wcmGUI::openFieldset("Carte");
		wcmGUI::renderTextField('echoAirport_mapTitle[]', 	$echoAirport->mapTitle, 	"Titre");	
		wcmGUI::renderTextField('echoAirport_mapCredits[]',	$echoAirport->mapCredits, 	"Crédits");
		//wcmGUI::renderTextField('echoAirport_mapFile[]',	$echoAirport->mapFile, 		"Fichier");
		
		$idForPicture = 'airportMapFile'.uniqid();
		wcmGUI::renderHiddenField('echoAirport_mapFile2[]', $echoAirport->mapFile, array('id' => $idForPicture.'2'));
	    wcmGUI::renderFileField('echoAirport_mapFile[]', 			"Fichier", array("id"=>$idForPicture));
	    if (!empty($echoAirport->mapFile)) echo "<a href='".$config['wcm.backOffice.url'].$config['wcm.backOffice.photosPathLesEchos'].DIRECTORY_SEPARATOR.$echoAirport->mapFile."' target='_blank'>cliquez ici pour voir l'image :  ".$echoAirport->mapFile."</a>";
	    /*
		$idForPicture = 'airportMapFile'.uniqid();
		$photoId = 'photo_'.$idForPicture;
		wcmGUI::renderHiddenField('echoAirport_mapFile[]', $echoAirport->mapFile, array('id' => $photoId));
	    echo '<div id="photo_chapter">';
			echo '<li>';
			echo '<label>Fichier</label>';
			$selectedPicture = 'selectedPicture_'.$idForPicture;
			if ($echoAirport->mapFile)
				$src = $echoAirport->mapFile;
			else
				$src = 'img/none.gif';
			echo '<img style="float:left; margin-bottom: 5px" width="100px" id="'.$selectedPicture.'" src="'.$src.'" onClick="openmodal(\''.getConst(_BIZ_PHOTOS_ADD).'\', \'650\' ); modalPopup(\'chooseFileLesEchos\',\'chooseFileLesEchos\', null, \''.$idForPicture.'\'); return false;" style="cursor:pointer" alt="Click to choose" title="Click to choose">';
			echo '<em class="removePicture" alt="Supprimer la photo" title="Supprimer la photo" style="cursor:pointer" onClick="removePicture(\''.$idForPicture.'\')"></em>';
			echo "</li>";
		echo '</div>';
		*/
		wcmGUI::closeFieldset();
		
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";