<?php
/**
 * Project:     WCM
 * File:        modules/editorial/person/schedule.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
	$bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
	
    echo '<div class="zone">';
    
    wcmModule(  'business/relationship/main',
                 array('kind' => wcmBizrelation::IS_RELATED_TO, 
                      'destinationClass' => 'organisation',
                      'classFilter' =>  'organisation',
                      'prefix' => '_wcm_rel_organisation_',
                      'resultStyle' => 'grid',
                      'createTab' => true,
                      'searchEngine' => $config['wcm.search.engine'],
                      'uid' => $bizobject->getClass().'SearchIdLocation'));

    echo '</div>';

		echo '<div style="clear:both;">&nbsp;</div>';

    echo '</div>';
