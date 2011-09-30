<?php
/**
 * Project:     WCM
 * File:        modules/editorial/article/content.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $pages = ($bizobject->id) ? $bizobject->getChapters() : array(new chapter());
    echo '<div class="zone">';

    wcmModule('business/shared/metacontent');

    echo '<div id="pages" style="clear: both;">';
    foreach ($pages as $page)
    {
        wcmModule('business/editorial/article/chapter', array('page' => $page));
    }
    echo '</div>';

    echo '</div>';
