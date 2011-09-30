<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoTakeOuts.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoTakeOuts_text_');
    $echoTakeOuts = ($bizobject->id) ? $bizobject->getEchoTakeOuts() : array(new echoTakeOuts());

    if (empty($echoTakeOuts))  
    	wcmModule('business/lesEchos/echoTakeOutsModule');
	else 
	{
	    foreach ($echoTakeOuts as $result)
		{
		        wcmModule('business/lesEchos/echoTakeOutsModule', array('echoTakeOuts' => $result));
	    }
	}