<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoEvent.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoEvent_text_');
    $echoEvent = ($bizobject->id) ? $bizobject->getEchoEvent() : array(new echoEvent());

	if (empty($echoEvent))  
    	wcmModule('business/lesEchos/echoEventModule');
	else 
	{ 
	    foreach ($echoEvent as $result)
		{
		        wcmModule('business/lesEchos/echoEventModule', array('echoEvent' => $result));
	    }
	}