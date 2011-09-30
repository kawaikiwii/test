<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoEmbassyModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoEmbassy_text_');
    $echoEmbassy = getArrayParameter($params, 'echoEmbassy', new echoEmbassy());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoEmbassyModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoEmbassy'
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
    	   $info .= '<li><a href="#" onclick="addEchoEmbassy(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';

    $title = (isset($echoEmbassy->title)) ? "Ambassade" . ' ' . $echoEmbassy->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoEmbassy" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoEmbassyFieldset'. $echoEmbassy->id));

		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoEmbassy_id[]', $echoEmbassy->id, array('id' => uniqid()));
		wcmGUI::renderTextField('echoEmbassy_title[]',		$echoEmbassy->title, 		"Nom");
		//wcmGUI::renderDropdownField('echoEmbassy_kind[]', echoEmbassy::getKind(), 	$echoEmbassy->kind, "Type");
		wcmGUI::closeFieldset();

		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextArea('echoEmbassy_address[]', 	$echoEmbassy->address, 		"Adresse", array('rows'=>3));
		wcmGUI::renderTextField('echoEmbassy_postalCode[]',	$echoEmbassy->postalCode, 	"Code postal");
		//wcmGUI::renderTextField('echoEmbassy_city[]',		$echoEmbassy->city, 		"Ville");
		//wcmGUI::renderTextField('echoEmbassy_country[]',	$echoEmbassy->country, 		"Pays");

		echo '<div id="echoEmbassy_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoEmbassy_cityGeoNameId[]', $echoEmbassy->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoEmbassy_city[]', $echoEmbassy->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';

    	echo '<div id="echoEmbassy_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoEmbassy_countryGeoNameId[]', $echoEmbassy->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoEmbassy_country[]', $echoEmbassy->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';

		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoEmbassy->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoEmbassy_longitude[]', $echoEmbassy->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoEmbassy_latitude[]', $echoEmbassy->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoEmbassy_autogmap[]', $echoEmbassy->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	    
	    if (!empty($echoEmbassy->latitude) && !empty($echoEmbassy->longitude))
		{
			$position = urlencode($echoEmbassy->latitude." ".$echoEmbassy->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}

		wcmGUI::closeFieldset();

		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoEmbassy_phone[]', 		$echoEmbassy->phone, 		"Téléphone");
		wcmGUI::renderTextField('echoEmbassy_email[]',		$echoEmbassy->email, 		"Email");
		wcmGUI::renderTextField('echoEmbassy_web[]',		$echoEmbassy->web, 			"Web");
		wcmGUI::closeFieldset();

		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";