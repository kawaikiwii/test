<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoHousingModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoHousing_text_');
    $echoHousing = getArrayParameter($params, 'echoHousing', new echoHousing());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoHousingModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoHousing'
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
    	   $info .= '<li><a href="#" onclick="addEchoHousing(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoHousing->title)) ? "Se loger" . ' ' . $echoHousing->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoHousing" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoHousingFieldset'. $echoHousing->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoHousing_id[]', $echoHousing->id, array('id' => uniqid()));
		wcmGUI::renderDropdownField('echoHousing_kind[]', echoHousing::getKind(), $echoHousing->kind, "Type");
		wcmGUI::renderTextField('echoHousing_title[]',		$echoHousing->title, 		"Nom");
		wcmGUI::renderTextArea('echoHousing_commentDates[]',$echoHousing->commentDates, "Description");
		wcmGUI::renderTextArea('echoHousing_commentPrice[]',$echoHousing->commentPrice, "Commentaires Prix");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextArea('echoHousing_address[]', 	$echoHousing->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoHousing_postalCode[]',	$echoHousing->postalCode, 	"Code postal");
		
		echo '<div id="echoHousing_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoHousing_cityGeoNameId[]', $echoHousing->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoHousing_city[]', $echoHousing->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoHousing_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoHousing_countryGeoNameId[]', $echoHousing->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoHousing_country[]', $echoHousing->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoHousing->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoHousing_longitude[]', $echoHousing->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoHousing_latitude[]', $echoHousing->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoHousing_autogmap[]', $echoHousing->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	   
	    if (!empty($echoHousing->latitude) && !empty($echoHousing->longitude))
		{
			$position = urlencode($echoHousing->latitude." ".$echoHousing->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoHousing_phone[]', 	$echoHousing->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoHousing_email[]',		$echoHousing->email, 		"Email");
		wcmGUI::renderTextField('echoHousing_web[]',		$echoHousing->web, 		"Web");
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";