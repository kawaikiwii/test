<?php
/**
 * Project:     WCM
 * File:        modules/editorial/organisation/schedule.php
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
	
    echo '<div class="zone">';
    
    wcmModule(  'business/relationship/main',
                 array('kind' => wcmBizrelation::IS_RELATED_TO, 
                      'destinationClass' => 'person',
                      'classFilter' =>  'person',
                      'prefix' => '_wcm_rel_person_',
                      'resultStyle' => 'grid',
                      'createTab' => false,
                      'searchEngine' => $config['wcm.search.engine'],
                      'uid' => $bizobject->getClass().'SearchIdLocation'));

    echo '</div>';

		echo '<div style="clear:both;">&nbsp;</div>';

    echo '</div>';
