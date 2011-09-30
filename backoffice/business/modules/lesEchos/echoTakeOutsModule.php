<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoTakeOutsModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoTakeOuts_text_');
    $echoTakeOuts = getArrayParameter($params, 'echoTakeOuts', new echoTakeOuts());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoTakeOutsModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoTakeOuts'
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
    	   $info .= '<li><a href="#" onclick="addEchoTakeOuts(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoTakeOuts->title)) ? "Manger/Sortir" . ' ' . $echoTakeOuts->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoTakeOuts" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoTakeOutsFieldset'. $echoTakeOuts->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoTakeOuts_id[]', $echoTakeOuts->id, array('id' => uniqid()));
		wcmGUI::renderDropdownField('echoTakeOuts_kind[]', echoTakeOuts::getKind(), $echoTakeOuts->kind, "Type");
		wcmGUI::renderTextField('echoTakeOuts_title[]',		$echoTakeOuts->title, 		"Nom");
		wcmGUI::renderTextArea('echoTakeOuts_commentDates[]',$echoTakeOuts->commentDates, "Commentaires Dates");
		wcmGUI::renderTextArea('echoTakeOuts_commentPrice[]',$echoTakeOuts->commentPrice, "Commentaires Prix");
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Cordonnées");
		wcmGUI::renderTextArea('echoTakeOuts_address[]', 	$echoTakeOuts->address, 		"Adresse", array('rows'=>3));	
		wcmGUI::renderTextField('echoTakeOuts_postalCode[]',	$echoTakeOuts->postalCode, 	"Code postal");
		
		echo '<div id="echoTakeOuts_cityfields'.uniqid().'">';
		wcmGUI::openFieldset("Ville");
		$hCityGeoNameId = 'cityGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoTakeOuts_cityGeoNameId[]', $echoTakeOuts->cityGeoNameId, array('id' => $hCityGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccitylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCityGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoTakeOuts_city[]', $echoTakeOuts->city, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
    	echo '<div id="echoTakeOuts_countryfields'.uniqid().'">';
		wcmGUI::openFieldset("Pays");
	    $hCountryGeoNameId = 'countryGeoNameId'.uniqid();
		wcmGUI::renderHiddenField('echoTakeOuts_countryGeoNameId[]', $echoTakeOuts->countryGeoNameId, array('id' => $hCountryGeoNameId));
	    $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.geoloccountrylist.php';
	    $acOptions = array('url' => $url, 'paramName' => 'prefix', 'afterUpdateElement'=>'function(text, li) {document.getElementById("'.$hCountryGeoNameId.'").value=li.id;}');
	    wcmGUI::renderCommonListField2('echoTakeOuts_country[]', $echoTakeOuts->country, null, $acOptions, uniqid());
		wcmGUI::closeFieldset();
		echo '</div>';
		
		$longId = 'long'.uniqid();
		$latId = 'lat'.uniqid();
		if ($echoTakeOuts->autogmap) 
		{
			$longReadOnly = "1";
			$latReadOnly = "1";
		}
		else
		{
			$longReadOnly = "";
			$latReadOnly = "";
		} 
		
		relaxGUI::renderEchoTextFieldReadOnly('echoTakeOuts_longitude[]', $echoTakeOuts->longitude, 'Longitude', array('id' => $longId), $longReadOnly);
	    relaxGUI::renderEchoTextFieldReadOnly('echoTakeOuts_latitude[]', $echoTakeOuts->latitude, 'Latitude', array('id' => $latId), $latReadOnly );
	    relaxGUI::renderEchosBooleanUField('echoTakeOuts_autogmap[]', $echoTakeOuts->autogmap, 'Coordonnées automatiques avec Google Map basées sur l\'adresse saisie (décochez pour modification manuelle)', $longId, $latId);
	    
	    if (!empty($echoTakeOuts->latitude) && !empty($echoTakeOuts->longitude))
		{
			$position = urlencode($echoTakeOuts->latitude." ".$echoTakeOuts->longitude);
			$url = "http://maps.google.fr/maps?q=".$position;
			echo "<ul><li><a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' align='middle'> Vérifier sous googlemap</a></li></ul>";
		}
		wcmGUI::closeFieldset();
		
		wcmGUI::openFieldset("Infos contact");
		wcmGUI::renderTextField('echoTakeOuts_phone[]', 	$echoTakeOuts->phone, 		"Téléphone");	
		wcmGUI::renderTextField('echoTakeOuts_email[]',		$echoTakeOuts->email, 		"Email");
		wcmGUI::renderTextField('echoTakeOuts_web[]',		$echoTakeOuts->web, 		"Web");
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";