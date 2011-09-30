<?php
/**
 * Project:     WCM
 * File:        modules/editorial/article/inserts.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('inserts_text_');
    $inserts = ($bizobject->id) ? $bizobject->getInserts() : array(new inserts());

	foreach ($inserts as $insert)
	{
	        wcmModule('business/editorial/article/insertsModule', array('inserts' => $insert));
    }