<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoStation.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoStation_text_');
    $echoStation = ($bizobject->id) ? $bizobject->getEchoStation() : array(new echoStation());

    if (empty($echoStation))  
    	wcmModule('business/lesEchos/echoStationModule');
	else 
	{ 
	    foreach ($echoStation as $result)
		{
		        wcmModule('business/lesEchos/echoStationModule', array('echoStation' => $result));
	    }
	}