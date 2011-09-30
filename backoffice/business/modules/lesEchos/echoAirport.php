<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoAirport.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoAirport_text_');
    $echoAirport = ($bizobject->id) ? $bizobject->getEchoAirport() : array(new echoAirport());

    if (empty($echoAirport))  
    	wcmModule('business/lesEchos/echoAirportModule');
	else 
	{
		foreach ($echoAirport as $result)
		{
	        wcmModule('business/lesEchos/echoAirportModule', array('echoAirport' => $result));
    	}
	}