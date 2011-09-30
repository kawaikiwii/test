<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoSlideshow.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('echoSlideshow_text_');
    $echoSlideshow = ($bizobject->id) ? $bizobject->getEchoSlideshow() : array(new echoSlideshow());

    if (empty($echoSlideshow))  
    	wcmModule('business/lesEchos/echoSlideshowModule');
	else 
	{
	    foreach ($echoSlideshow as $result)
		{
		        wcmModule('business/lesEchos/echoSlideshowModule', array('echoSlideshow' => $result));
	    }
	}