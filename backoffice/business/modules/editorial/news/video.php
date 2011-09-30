<?php
/**
 * Project:     WCM
 * File:        modules/editorial/news/video.php
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

wcmGUI::renderTextArea('embedVideo', $bizobject->embedVideo, 'Youtube', array('cols' => '60', 'rows' => '5'));

echo "<br><br>";

    wcmModule(  'business/relationship/main',
                 array('kind' => wcmBizrelation::IS_RELATED_TO,
                      'destinationClass' => 'video',
                      'classFilter' =>  'video',
                      'prefix' => '_wcm_rel_videos_',
                      'resultStyle' => 'grid',
                      'createTab' => true,
                      'searchEngine' => $config['wcm.search.engine'],
                      'uid' => $bizobject->getClass().'SearchIdVideo'
		));

    echo '</div>';

		echo '<div style="clear:both;">&nbsp;</div>';

    echo '</div>';
