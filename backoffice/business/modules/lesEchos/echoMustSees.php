<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoMustSees.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoMustSees_text_');
    $echoMustSees = ($bizobject->id) ? $bizobject->getEchoMustSees() : array(new echoMustSees());

    if (empty($echoMustSees))  
    	wcmModule('business/lesEchos/echoMustSeesModule');
	else 
	{
	    foreach ($echoMustSees as $result)
		{
		        wcmModule('business/lesEchos/echoMustSeesModule', array('echoMustSees' => $result));
	    }
	}