<?php
/**
 * Project:     WCM
 * File:        modules/editorial/event/infos.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();

	wcmGUI::openFieldset('');
		wcmGUI::renderTextField('price', $bizobject->price, _BIZ_LOCATION_PRICE);
		
		$schedule = new schedule();
		$schedule->startsAt = $bizobject->startDate;
		$schedule->endsAt = $bizobject->endDate;
		/*
		 * Issue with no auto-refresh object, so this is NOT a good solution at this time !!! (below)
		 */
		$location = $bizobject->getRelations();
		$locationId = (isset($location[0])) ? $location[0]->destinationId : 0;
		
		//if ($locationId != 0)
		//{
			wcmModule('business/shared/schedule', array('schedule' => $schedule, 'bizObjectClass' => $bizobject->getClass(), 'targetId' => $locationId));
		//}
		//else wcmModule('business/shared/schedule', array('schedule' => $schedule, 'bizObjectClass' => $bizobject->getClass()));
		
		
		wcmGUI::renderTextArea('dateComment', $bizobject->dateComment, _BIZ_LOCATION_DATECOMMENT);
		wcmGUI::renderTextArea('pressContact', $bizobject->pressContact, _BIZ_LOCATION_PRESSCONTACT);
	    wcmGUI::renderTextField('phone', $bizobject->phone, _BIZ_LOCATION_PHONE);
	    wcmGUI::renderTextField('email', $bizobject->email, _BIZ_LOCATION_EMAIL);
	    wcmGUI::renderTextField('website', $bizobject->website, _BIZ_LOCATION_WEBSITE);
		echo HELP_INPUT_WEBSITE;
    wcmGUI::closeFieldset();
