<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoHospital.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoHospital_text_');
    $echoHospital = ($bizobject->id) ? $bizobject->getEchoHospital() : array(new echoHospital());

    if (empty($echoHospital))  
    	wcmModule('business/lesEchos/echoHospitalModule');
	else 
	{
	    foreach ($echoHospital as $result)
		{
		        wcmModule('business/lesEchos/echoHospitalModule', array('echoHospital' => $result));
	    }
	}