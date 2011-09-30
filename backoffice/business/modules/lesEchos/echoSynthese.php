<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/propertiessynthese.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    
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
    
    function translateType($type)
    {
	    switch ($type) 
	    {
	    case "Embassy":
	        return "Ambassade";
	    case "Hospital":
	        return "Hôpital";
	    case "Police":
	        return "Police";
	    case "Airport":
	        return "Aéroport";
		case "Event":
	        return "Evènement";
		case "Housing":
	        return "Se loger";
		case "MustSees":
	        return "A voir/A faire";
		case "TakeOuts":
	        return "Manger/Sortir";
		case "Station":
	        return "Gare";
		case "Show":
	        return "Salon";
		} 
    }
    echo "<table cellpadding=4 cellspacing=4>";
    echo "<tr style='font-weight:bold;'><td>TYPE</td><td>TITRE</td><td>LATITUDE</td><td>LONGITUDE</td><td>CHECK</td><td>&nbsp;</td></tr>";
    
    
    foreach ($listEcho as $list)
    {
    	$i = 1;
    	foreach ($list as $data)
    	{
    		if (!empty($data->title)) 
    		{
    			$objType = str_replace("echo", "", $data->getClass());
    			 echo "<tr><td>".translateType($objType)." ".$i."</td>";
    			 echo "<td style='font-weight:bold;'>".$data->title."</td>";  			 
    			 
    			if (!empty($data->latitude) && !empty($data->longitude))
    			{
    				$position = urlencode($data->latitude." ".$data->longitude);
					$url = "http://maps.google.fr/maps?q=".$position;
					echo "<td>".$data->latitude."</td><td>".$data->longitude."</td><td align='center'><a href='".$url."' target='_blank'><img src='./img/icons/globe_search.png' border='0' height='12' align='middle'></a></td>";
				}
    			else
    				echo "<td colspan='3' style='background:red'>&nbsp;</td>";
    			
    			$info = "<td>&nbsp;</td></tr>";		

    			switch ($data->getClass()) 
    			{
    				case "echoEvent":
        			case "echoShow":
        			if (isset($data->endDate) && !empty($data->endDate))
		    		{	    				
	    				$datejour = date('Ymd');				
	    				$dfin = str_replace("-", "", $data->endDate);    				
	    				if ($dfin < $datejour) $info = "<td style='width:150px;background:red;text-align:center'>A expiré</td></tr>";    				
		    		}
		    		else $info = "<td style='width:150px;background:red;text-align:center'>Pas de date de fin</td></tr>";	
		    		
    				if (!isset($data->startDate))
    					$info = "<td style='width:150px;background:red;text-align:center'>Date début manquante</td></tr>";	
		    		
       				break;
    			}
    			
    			echo $info;
    		}
    		$i++;
    	}
    }
    
     echo "</table>";
    
    /*
    foreach ($listEcho as $list)
    {
    	echo "<ul>";
    	$i = 1;
    	foreach ($list as $data)
    	{
    		if (!empty($data->title)) 
    		{
    			$objType = str_replace("echo", "", $data->getClass());
    			echo "<li style='list-style: square inside; color:blue'><span style='color:black'>".translateType($objType)." ".$i." : <b>".$data->title."</b>";
    			
    			if (!empty($data->latitude) && !empty($data->longitude))
    			{
    				echo " (latitude:".$data->latitude." / longitude:".$data->longitude.")";
    				$position = urlencode($data->latitude." ".$data->longitude);
					$url = "http://maps.google.fr/maps?q=".$position;
					echo "  <a href='".$url."' target='_blank'><img src='/img/icons/globe_search.png' border='0' height='12' align='middle'> Vérifier sous googlemap</a>";
    			}
    			else
    				echo "<span style='color:red'> (Attention latitude et longitude manquantes !) </span>";	
    			echo "</span></li>";
    		}
    		$i++;
    	}
    	echo "</ul>";
    }
    */
    wcmGUI::closeFieldset();
    
    echo '</div>';
