<?php
/**
 * Project:     WCM
 * File:        modules/shared/media.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    $onlyUniverse = getArrayParameter($params, 'onlyUniverse', false);
    $allowedUniverse = getArrayParameter($params, 'allowedUniverse', '');
    
	$actions = '<ul class="actions">'
             . '<li><a href="#" onClick="switchPane(\'left\');">' . _BIZ_SWITCH_PANE_LEFT . '</a></li>'
             . '<li><a href="#" onClick="switchPane(\'regular\');">' . _BIZ_SWITCH_PANE_CENTER . '</a></li>'
             . '<li><a href="#" onClick="switchPane(\'right\');">' . _BIZ_SWITCH_PANE_RIGHT . '</a></li>'
             . '</ul>';

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_BIZ_PHOTOS, true, $actions);

    wcmModule(  'business/relationship/main',
                 array('kind' => wcmBizrelation::IS_COMPOSED_OF,
                      'destinationClass' => 'photo',
                      'classFilter' =>  'photo',
                      'prefix' => '_wcm_rel_photos_',
                      'resultStyle' => 'grid',
                      'createTab' => true,
                      'searchEngine' => $config['wcm.search.engine'],
		      		  'createModule' => 'business/subForms/uploadPhoto',
                 	  'onlyUniverse' => $onlyUniverse,
                 	  'allowedUniverse' => $allowedUniverse,
                      'uid' => $bizobject->getClass().'SearchId'
		));

    wcmGUI::closeCollapsablePane();
    echo '</div>';

