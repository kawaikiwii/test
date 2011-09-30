<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoMustSeesModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoMustSees_text_');
    $echoMustSees = getArrayParameter($params, 'echoMustSees', new echoMustSees());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoMustSeesModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoMustSees'
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
    	   $info .= '<li><a href="#" onclick="addEchoMustSees(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoMustSees->title)) ? "A voir/A faire" . ' ' . $echoMustSees->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoMustSees" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoMustSeesFieldset'. $echoMustSees->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoMustSees_id[]', $echoMustSees->id, array('id' => uniqid()));
		wcmGUI::renderDropdownField('echoMustSees_kind[]', echoMustSees::getKind(), $echoMustSees->kind, "Type");
		wcmGUI::renderTextField('echoMustSees_title[]',		$echoMustSees->title, 		"Nom");
		wcmGUI::renderTextArea('echoMustSees_commentDates[]',$echoMustSees->commentDates, "Commentaires Dates");
		wcmGUI::renderTextArea('echoMustSees_commentPrice[]',$echoMustSees->commentPrice, "Commentaires Prix");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextArea('echoMustSees_address[]', 	$echoMustSees->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoMustSees_postalCode[]',	$echoMustSees->postalCode, 	"Code postal");
		
		echo '<div id="echoMustSees_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoMustSees_cityGeoNameId[]', $echoMustSees->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoMustSees_city[]', $echoMustSees->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoMustSees_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoMustSees_countryGeoNameId[]', $echoMustSees->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoMustSees_country[]', $echoMustSees->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoMustSees->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoMustSees_longitude[]', $echoMustSees->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoMustSees_latitude[]', $echoMustSees->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoMustSees_autogmap[]', $echoMustSees->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	   
	    if (!empty($echoMustSees->latitude) && !empty($echoMustSees->longitude))
		{
			$position = urlencode($echoMustSees->latitude." ".$echoMustSees->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoMustSees_phone[]', 	$echoMustSees->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoMustSees_email[]',		$echoMustSees->email, 		"Email");
		wcmGUI::renderTextField('echoMustSees_web[]',		$echoMustSees->web, 		"Web");
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";