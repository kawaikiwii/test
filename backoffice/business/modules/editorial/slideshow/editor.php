<?php
/**
 * Project:     WCM
 * File:        modules/editorial/slideshow/content.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
    //$channels = channel::getChannelHierarchy();
    //$config = wcmConfig::getInstance();

    $contents = ($bizobject->id) ? $bizobject->getContents() : array(new content());

    echo '<div class="zone">';

	echo '<div id="contents" style="clear: both;">';
    foreach ($contents as $content)
    {
        wcmModule('business/shared/content', array('content' => $content, 'bizObjectClass' => $bizobject->getClass()));
        /* @todo :: embedded relation picker
         * same module as in media, should be embedable
         wcmGUI::openFieldset(_ATTACHED_MEDIA);
         wcmModule('business/editorial/relation/builder', $___);
         wcmGUI::closeFieldset();
        */
    }
    echo '</div>';

    /*
     * wcmModule('business/shared/metacontent');

    wcmGUI::openCollapsablePane(_BIZ_PHOTOS);

    wcmModule('business/relationship/main',
        array('kind' => wcmBizrelation::IS_COMPOSED_OF,
              'destinationClass' => 'photo',
              'classFilter' => 'photo',
              'prefix' => '_wcm_rel_photos_',
              'resultStyle' => 'grid',
              'createTab' => true,
              'searchEngine' => $config['wcm.search.engine'],
              'createModule' => 'business/subForms/uploadPhoto',
              'uid' => 'slideshowSearchId'));

    wcmGUI::closeCollapsablePane();
    */

    echo '</div>';
