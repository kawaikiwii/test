<?php
/**
 * Project:     WCM
 * File:        modules/shared/schedule.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
	//print_r($bizobject);
	$initialClass = getArrayParameter($params, 'bizObjectClass', '');
	/*
	 * The object is not automatically refresh, so below if we decoment,we have to save 2 times before it take it in count
	 */
	$targetId = getArrayParameter($params, 'targetId', '');
	$uniqid = 'schedule_' . $initialClass;
	$schedule = getArrayParameter($params, 'schedule', new schedule());
	
	$start = (isset($schedule->startsAt)) ? $schedule->startsAt : date('Y-m-d');
	$end = (isset($schedule->endsAt)) ? $schedule->endsAt : date('Y-m-d');
	
	//print_r($bizobject);
	//wcmGUI::openCollapsablePane('');
		wcmGUI::openFieldset("");
			wcmGUI::renderDateField($uniqid.'_startsAt', substr($start, 0, 10), _BIZ_SCHEDULES_FIRST_SHOWDATE);
			wcmGUI::renderDateField($uniqid.'_endsAt',  substr($end, 0, 10), _BIZ_SCHEDULES_LAST_SHOWDATE);
			//wcmGUI::renderHiddenField($uniqid.'_destinationId', $targetId);
		wcmGUI::closeFieldset();
	//wcmGUI::closeCollapsablePane();
	
?>

