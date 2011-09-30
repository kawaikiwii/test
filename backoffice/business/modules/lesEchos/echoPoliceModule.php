<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoPoliceModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoPolice_text_');
    $echoPolice = getArrayParameter($params, 'echoPolice', new echoPolice());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoPoliceModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoPolice'
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
    	   $info .= '<li><a href="#" onclick="addEchoPolice(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoPolice->title)) ? "Police" . ' ' . $echoPolice->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoPolice" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoPoliceFieldset'. $echoPolice->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoPolice_id[]', $echoPolice->id, array('id' => uniqid()));
		wcmGUI::renderTextField('echoPolice_title[]',		$echoPolice->title, 		"Nom");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextArea('echoPolice_address[]', 	$echoPolice->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoPolice_postalCode[]',	$echoPolice->postalCode, 	"Code postal");
		
		echo '<div id="echoPolice_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoPolice_cityGeoNameId[]', $echoPolice->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoPolice_city[]', $echoPolice->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoPolice_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoPolice_countryGeoNameId[]', $echoPolice->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoPolice_country[]', $echoPolice->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoPolice->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoPolice_longitude[]', $echoPolice->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoPolice_latitude[]', $echoPolice->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoPolice_autogmap[]', $echoPolice->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	   
	    if (!empty($echoPolice->latitude) && !empty($echoPolice->longitude))
		{
			$position = urlencode($echoPolice->latitude." ".$echoPolice->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoPolice_phone[]', 	$echoPolice->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoPolice_email[]',		$echoPolice->email, 		"Email");
		wcmGUI::renderTextField('echoPolice_web[]',		$echoPolice->web, 		"Web");
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";