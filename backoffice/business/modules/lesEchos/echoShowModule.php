<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoShowModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoShow_text_');
    $echoShow = getArrayParameter($params, 'echoShow', new echoShow());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoShowModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoShow'
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
    	   $info .= '<li><a href="#" onclick="addEchoShow(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoShow->title)) ? "Evènement" . ' ' . $echoShow->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoShow" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoShowFieldset'. $echoShow->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoShow_id[]', $echoShow->id, array('id' => uniqid()));
		//wcmGUI::renderDropdownField('echoShow_kind[]', echoShow::getKind(), $echoShow->kind, "Type");
		wcmGUI::renderTextField('echoShow_title[]',		$echoShow->title, 		"Nom");
		wcmGUI::renderTextField('echoShow_startDate[]', 	$echoShow->startDate, 	"Début (AAAA-mm-jj)");
    	wcmGUI::renderTextField('echoShow_endDate[]', 		$echoShow->endDate, 	"Fin (AAAA-mm-jj)");
    	wcmGUI::renderTextArea('echoShow_commentDates[]', $echoShow->commentDates, "Commentaires Dates");
		wcmGUI::renderTextArea('echoShow_description[]', $echoShow->description, 	"Description");
		wcmGUI::renderTextField('echoShow_price[]',		$echoShow->price, 		"Prix");
		wcmGUI::renderTextArea('echoShow_commentPrice[]', $echoShow->commentPrice, "Commentaire Prix");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextField('echoShow_placeName[]',	$echoShow->placeName, 		"Nom du lieu");
		wcmGUI::renderTextArea('echoShow_address[]', 		$echoShow->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoShow_postalCode[]',	$echoShow->postalCode, 	"Code postal");
		
		echo '<div id="echoShow_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoShow_cityGeoNameId[]', $echoShow->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoShow_city[]', $echoShow->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoShow_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoShow_countryGeoNameId[]', $echoShow->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoShow_country[]', $echoShow->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoShow->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoShow_longitude[]', $echoShow->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoShow_latitude[]', $echoShow->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoShow_autogmap[]', $echoShow->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	    
	    if (!empty($echoShow->latitude) && !empty($echoShow->longitude))
		{
			$position = urlencode($echoShow->latitude." ".$echoShow->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoShow_phone[]', 	$echoShow->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoShow_email[]',	$echoShow->email, 		"Email");
		wcmGUI::renderTextField('echoShow_web[]',		$echoShow->web, 		"Web");
		wcmGUI::closeFieldset();
        
		wcmGUI::openFieldset("Photo");
		wcmGUI::renderTextField('echoShow_linkTitle[]', 	$echoShow->linkTitle, 		"Titre");	
		wcmGUI::renderTextField('echoShow_linkCredits[]',	$echoShow->linkCredits, 	"Crédits");
		//wcmGUI::renderTextField('echoShow_linkFile[]',		$echoShow->linkFile, 		"Fichier");
		$idForPicture = 'linkFile'.uniqid();
		wcmGUI::renderHiddenField('echoShow_linkFile2[]', $echoShow->linkFile, array('id' => $idForPicture.'2'));
	    wcmGUI::renderFileField('echoShow_linkFile[]', 			"Fichier", array("id"=>$idForPicture));
	    if (!empty($echoShow->linkFile)) echo "<a href='".$config['wcm.backOffice.url'].$config['wcm.backOffice.photosPathLesEchos'].DIRECTORY_SEPARATOR.$echoShow->linkFile."' target='_blank'>cliquez ici pour voir l'image :  ".$echoShow->linkFile."</a>";
	    
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";