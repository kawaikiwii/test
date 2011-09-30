<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoHospitalModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoHospital_text_');
    $echoHospital = getArrayParameter($params, 'echoHospital', new echoHospital());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoHospitalModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoHospital'
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
    	   $info .= '<li><a href="#" onclick="addEchoHospital(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoHospital->title)) ? "Hôpital" . ' ' . $echoHospital->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoHospital" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoHospitalFieldset'. $echoHospital->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoHospital_id[]', $echoHospital->id, array('id' => uniqid()));
		wcmGUI::renderTextField('echoHospital_title[]',		$echoHospital->title, 		"Nom");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextArea('echoHospital_address[]', 	$echoHospital->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoHospital_postalCode[]',	$echoHospital->postalCode, 	"Code postal");
		
		echo '<div id="echoHospital_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoHospital_cityGeoNameId[]', $echoHospital->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoHospital_city[]', $echoHospital->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoHospital_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoHospital_countryGeoNameId[]', $echoHospital->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoHospital_country[]', $echoHospital->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoHospital->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoHospital_longitude[]', $echoHospital->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoHospital_latitude[]', $echoHospital->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoHospital_autogmap[]', $echoHospital->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	   
	    if (!empty($echoHospital->latitude) && !empty($echoHospital->longitude))
		{
			$position = urlencode($echoHospital->latitude." ".$echoHospital->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoHospital_phone[]', 	$echoHospital->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoHospital_email[]',		$echoHospital->email, 		"Email");
		wcmGUI::renderTextField('echoHospital_web[]',		$echoHospital->web, 		"Web");
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";