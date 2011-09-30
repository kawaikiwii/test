<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoMap.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    
    $initialCoord = $bizobject->latitude."#".$bizobject->longitude;
    
    echo '<div class="zone">';
    
    wcmGUI::openFieldset("Géo localisation des différents lieux de ".$bizobject->title);
    
    $listEcho[] = $bizobject->getEchoEmbassy();
    $listEcho[] = $bizobject->getEchoHospital();
    $listEcho[] = $bizobject->getEchoPolice();
    $listEcho[] = $bizobject->getEchoAirport();
    $listEcho[] = $bizobject->getEchoHousing();
    $listEcho[] = $bizobject->getEchoTakeOuts();
    $listEcho[] = $bizobject->getEchoMustSees();
    $listEcho[] = $bizobject->getEchoEvent();
    $listEcho[] = $bizobject->getEchoStation();
    $listEcho[] = $bizobject->getEchoShow();
    
    $dataToIframe = "";
    
    foreach ($listEcho as $list)
    {
    	echo "<ul>";
    	$i = 1;
    	foreach ($list as $data)
    	{
    		if (!empty($data->title)) 
    		{
    			$imgMarker = "parachute.png"; 
			    $objType = str_replace("echo", "", $data->getClass());
			    
    			switch ($objType) 
			    {
			    case "Embassy":
			        $imgMarker = "building.png";
			        break;
			    case "Hospital":
			        $imgMarker = "medical.png";
			        break;
			    case "Police":
			        $imgMarker = "dive.png";
			        break;
			    case "Airport":
			        $imgMarker = "airport.png";
			        break;
			    case "Event":
			        $imgMarker = "entertain.png";
			        break;
			    case "Housing":
			        $imgMarker = "hotel.png";
			        break;
			    case "TakeOuts":
			        $imgMarker = "food.png";
			        break;
			    case "MustSees":
			        $imgMarker = "scenic.png";
			        break;
			    case "Station":
			        $imgMarker = "boatramp.png";
			        break;
			    case "Show":
			        $imgMarker = "sski.png";
			        break;
			    } 
    			$dataToIframe .= $data->latitude."#".$data->longitude."#".$data->title."#".$imgMarker."#".$data->address."#".$data->postalCode."#".$data->city."@";
    		}	
    		$i++;
    	}
    	echo "</ul>";
    }    
    
    $dataToIframe = urlencode($dataToIframe);
    $initialCoord = urlencode($initialCoord);    
    echo '<center><iframe src="'.$config['wcm.backOffice.url'].'business/modules/lesEchos/scriptMap.php?init='.$initialCoord.'&data='.$dataToIframe.'" name="gmap" height="501" width="801"></iframe></center> ';  
    wcmGUI::closeFieldset();
    
    echo '</div>';
