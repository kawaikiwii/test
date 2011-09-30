<?php
/**
 * Project:     WCM
 * File:        modules/editorial/prevision/editor.php
 *
 * @copyright   (c)2011 relaxnews
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
    }
    echo '</div>';

    echo '</div>';