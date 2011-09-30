<?php
/**
 * Project:     WCM
 * File:        modules/editorial/article/pages.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $pages = $bizobject->getChapters();
    if (!$pages) $pages = array(new chapter());

    echo '<div class="zone">';

    echo '<div id="pages" style="clear: both;">';
    foreach ($pages as $page)
    {
        wcmModule('business/editorial/article/chapter', array('page' => $page));
        /* @todo :: embedded relation picker
         * same module as in media, should be embedable
         wcmGUI::openFieldset(_ATTACHED_MEDIA);
         wcmModule('business/editorial/relation/builder', $___);
         wcmGUI::closeFieldset();
        */
    }
    echo '</div>';

    $menus = array(getConst(_ADD_PAGE) => '\'' . wcmModuleURL('business/editorial/article/chapter') . '\', \'pages\'');

    $info = '<ul class="chapter" style="clear: left;">';
    foreach ($menus as $title => $action)
    {
        $info .= '<li><a href="#" onclick="addPage(' . $action . ', this); return false;">' . $title . '</a></li>';
    }
    $info .= '</ul><a name="newpagebutton"></a>';
	echo $info;

	echo '</div>';
