<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoEmbassy.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoEmbassy_text_');
    $echoEmbassy = ($bizobject->id) ? $bizobject->getEchoEmbassy() : array(new echoEmbassy());

	if (empty($echoEmbassy))  
    	wcmModule('business/lesEchos/echoEmbassyModule');
	else 
	{  
    	foreach ($echoEmbassy as $result)
		{
	        wcmModule('business/lesEchos/echoEmbassyModule', array('echoEmbassy' => $result));
    	}
	}