<?php
/**
 * Project:     WCM
 * File:        modules/editorial/notice/editor.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
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

    $actions = '<ul class="actions">'
             . '<li><a href="#" onclick="openmodal(\'' . _BIZ_UPLOAD_PHOTO . '\'); modalPopup(\'changephoto\',\'new\', \'\'); return false;">' . _BIZ_UPLOAD_PHOTO . '</a></li>'
             . '</ul>';

    echo '</div>';
	
	//print_r(channel::getChannelHierarchy());
