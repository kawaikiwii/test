<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoEventModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoEvent_text_');
    $echoEvent = getArrayParameter($params, 'echoEvent', new echoEvent());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoEventModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoEvent'
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
    	   $info .= '<li><a href="#" onclick="addEchoEvent(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoEvent->title)) ? "Evènement" . ' ' . $echoEvent->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoEvent" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoEventFieldset'. $echoEvent->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoEvent_id[]', $echoEvent->id, array('id' => uniqid()));
		wcmGUI::renderDropdownField('echoEvent_kind[]', echoEvent::getKind(), $echoEvent->kind, "Type");
		wcmGUI::renderTextField('echoEvent_title[]',		$echoEvent->title, 		"Nom");
		wcmGUI::renderTextField('echoEvent_startDate[]', 	$echoEvent->startDate, 	"Début (AAAA-mm-jj)");
    	wcmGUI::renderTextField('echoEvent_endDate[]', 		$echoEvent->endDate, 	"Fin (AAAA-mm-jj)");
    	wcmGUI::renderTextArea('echoEvent_commentDates[]', $echoEvent->commentDates, "Commentaires Dates");
		wcmGUI::renderTextArea('echoEvent_description[]', $echoEvent->description, 	"Description");
		wcmGUI::renderTextField('echoEvent_price[]',		$echoEvent->price, 		"Prix");
		wcmGUI::renderTextArea('echoEvent_commentPrice[]', $echoEvent->commentPrice, "Commentaire Prix");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextField('echoEvent_placeName[]',	$echoEvent->placeName, 		"Nom du lieu");
		wcmGUI::renderTextArea('echoEvent_address[]', 		$echoEvent->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoEvent_postalCode[]',	$echoEvent->postalCode, 	"Code postal");
		
		echo '<div id="echoEvent_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoEvent_cityGeoNameId[]', $echoEvent->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoEvent_city[]', $echoEvent->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoEvent_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoEvent_countryGeoNameId[]', $echoEvent->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoEvent_country[]', $echoEvent->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoEvent->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoEvent_longitude[]', $echoEvent->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoEvent_latitude[]', $echoEvent->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoEvent_autogmap[]', $echoEvent->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	    
	    if (!empty($echoEvent->latitude) && !empty($echoEvent->longitude))
		{
			$position = urlencode($echoEvent->latitude." ".$echoEvent->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoEvent_phone[]', 	$echoEvent->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoEvent_email[]',	$echoEvent->email, 		"Email");
		wcmGUI::renderTextField('echoEvent_web[]',		$echoEvent->web, 		"Web");
		wcmGUI::closeFieldset();
        
		wcmGUI::openFieldset("Photo");
		wcmGUI::renderTextField('echoEvent_linkTitle[]', 	$echoEvent->linkTitle, 		"Titre");	
		wcmGUI::renderTextField('echoEvent_linkCredits[]',	$echoEvent->linkCredits, 	"Crédits");
		//wcmGUI::renderTextField('echoEvent_linkFile[]',		$echoEvent->linkFile, 		"Fichier");
		$idForPicture = 'linkFile'.uniqid();
		wcmGUI::renderHiddenField('echoEvent_linkFile2[]', $echoEvent->linkFile, array('id' => $idForPicture.'2'));
	    wcmGUI::renderFileField('echoEvent_linkFile[]', 			"Fichier", array("id"=>$idForPicture));
	    if (!empty($echoEvent->linkFile)) echo "<a href='".$config['wcm.backOffice.url'].$config['wcm.backOffice.photosPathLesEchos'].DIRECTORY_SEPARATOR.$echoEvent->linkFile."' target='_blank'>cliquez ici pour voir l'image :  ".$echoEvent->linkFile."</a>";
	    
		/*
		$idForPicture = 'linkFile'.uniqid();
		$photoId = 'photo_'.$idForPicture;
		wcmGUI::renderHiddenField('echoEvent_linkFile[]', $echoEvent->linkFile, array('id' => $photoId));
	    echo '<div id="photo_chapter">';
			echo '<li>';
			echo '<label>Fichier</label>';
			$selectedPicture = 'selectedPicture_'.$idForPicture;
			if ($echoEvent->linkFile)
				$src = $echoEvent->linkFile;
			else
				$src = 'img/none.gif';
			echo '<img style="float:left; margin-bottom: 5px" width="100px" id="'.$selectedPicture.'" src="'.$src.'" onClick="openmodal(\''.getConst(_BIZ_PHOTOS_ADD).'\', \'650\' ); modalPopup(\'chooseFileLesEchos\',\'chooseFileLesEchos\', null, \''.$idForPicture.'\'); return false;" style="cursor:pointer" alt="Click to choose" title="Click to choose">';
			echo '<em class="removePicture" alt="Supprimer la photo" title="Supprimer la photo" style="cursor:pointer" onClick="removePicture(\''.$idForPicture.'\')"></em>';
			echo "</li>";
		echo '</div>';
		*/
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";