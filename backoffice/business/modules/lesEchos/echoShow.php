<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoShow.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoShow_text_');
    $echoShow = ($bizobject->id) ? $bizobject->getEchoShow() : array(new echoShow());

    if (empty($echoShow))  
    	wcmModule('business/lesEchos/echoShowModule');
	else 
	{
		foreach ($echoShow as $result)
		{
	        wcmModule('business/lesEchos/echoShowModule', array('echoShow' => $result));
    	}
	}