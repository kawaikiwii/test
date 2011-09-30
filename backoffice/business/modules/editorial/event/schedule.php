<?php
/**
 * Project:     WCM
 * File:        modules/editorial/event/schedule.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
	/* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
	//$prefix = '_wcm_rel_';
	//$pk = '_br_treeview_dates';
	
	/*$schedules = ($bizobject->id) ? $bizobject->getSchedules() : array(new schedule());

    echo '<div class="zone">';

	foreach ($schedules as $schedule)
    {
		wcmModule('business/shared/schedule', array('schedule' => $schedule, 'bizObjectClass' => $bizobject->getClass()));
   	}*/


		

    echo '<div class="zone">';
    //wcmGUI::openCollapsablePane(_BIZ_SCHEDULES_DATES, true);

    wcmModule(  'business/relationship/main',
                 array('kind' => wcmBizrelation::IS_RELATED_TO, 
                      'destinationClass' => 'location',
                      'classFilter' =>  'location',
                      'prefix' => '_wcm_rel_locations_',
                      'resultStyle' => 'grid',
                      'createTab' => true,
                      'searchEngine' => $config['wcm.search.engine'],
                      'uid' => $bizobject->getClass().'SearchIdLocation'));

    //wcmGUI::closeCollapsablePane();
    echo '</div>';

		echo '<div style="clear:both;">&nbsp;</div>';

    echo '</div>';
