<?php
/**
 * Project:     WCM
 * File:        modules/editorial/newsletter/content.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    echo '<div class="zone">';
 
    wcmModule('business/shared/metacontent');

    wcmGUI::openCollapsablePane(_BIZ_NEWSLETTER_FORCED_CONTENT);
    wcmModule('business/relationship/main', 
        array('kind' => wcmBizrelation::IS_COMPOSED_OF,
              'destinationClass' => '',
              'classFilter' => '',
              'resultStyle' => 'list',
              'prefix' => '_wcm_rel_',
              'searchEngine' => $config['wcm.search.engine'],
              'uid' => 'reference',
              'createTab' => false,
              'createModule' => 'business/subForms/uploadPhoto'));

    wcmGUI::closeCollapsablePane();
    
    echo '</div>';