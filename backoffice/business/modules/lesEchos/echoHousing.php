<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoHousing.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoHousing_text_');
    $echoHousing = ($bizobject->id) ? $bizobject->getEchoHousing() : array(new echoHousing());

    if (empty($echoHousing))  
    	wcmModule('business/lesEchos/echoHousingModule');
	else 
	{
	    foreach ($echoHousing as $result)
		{
		        wcmModule('business/lesEchos/echoHousingModule', array('echoHousing' => $result));
	    }
	}