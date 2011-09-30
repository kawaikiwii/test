<?php
/**
 * Project:     WCM
 * File:        modules/editorial/personality/properties.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
    
	/* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
    
	echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_INFORMATIONS);
    wcmGUI::openFieldset();
    
	wcmGUI::renderTextField('firstName', 	$bizobject->firstName, 	_BIZ_LOCATION_FIRSTNAME);
    wcmGUI::renderTextField('lastName', 	$bizobject->lastName, 	_BIZ_LOCATION_LASTNAME);
	wcmGUI::renderTextField('job', 			$bizobject->job, 		_BIZ_LOCATION_JOBTITLE);
    
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
	
    echo '</div>';
