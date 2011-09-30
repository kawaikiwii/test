<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoPolice.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoPolice_text_');
    $echoPolice = ($bizobject->id) ? $bizobject->getEchoPolice() : array(new echoPolice());

    if (empty($echoPolice))  
    	wcmModule('business/lesEchos/echoPoliceModule');
	else 
	{
	    foreach ($echoPolice as $result)
		{
		        wcmModule('business/lesEchos/echoPoliceModule', array('echoPolice' => $result));
	    }
	}