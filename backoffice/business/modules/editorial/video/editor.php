<?php
/**
 * Project:     WCM
 * File:        modules/editorial/video/editor.php
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
	//wcmGUI::openCollapsablePane(_BIZ_VIDEO);

	
	foreach ($contents as $content)
    {
        wcmModule('business/shared/content', array('content' => $content, 'bizObjectClass' => $bizobject->getClass()));
    }
    echo '</div>';

    wcmGUI::openFieldset();

	wcmGUI::renderTextField('credits', $bizobject->credits, _BIZ_CREDITS);
	wcmGUI::renderTextField('url', $bizobject->url, _BIZ_VIDEO_URL);
	wcmGUI::renderTextArea('embed', $bizobject->embed, _BIZ_VIDEO_EMBED);

    wcmGUI::closeFieldset();

    //wcmGUI::closeCollapsablePane();

	echo '</div>';
	
	$actions = '<ul class="actions">'
             . '<li><a href="#" onclick="openmodal(\'' . _BIZ_UPLOAD_PHOTO . '\'); modalPopup(\'changephoto\',\'new\', \'\'); return false;">' . _BIZ_UPLOAD_PHOTO . '</a></li>'
             . '</ul>';

    echo '</div>';