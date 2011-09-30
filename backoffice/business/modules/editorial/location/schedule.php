<?php
/**
 * Project:     WCM
 * File:        modules/editorial/event/schedule.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();

    echo '<div class="zone">';

    wcmGUI::openCollapsablePane("_PUBLICATION_GENERATION");
    wcmGUI::openFieldset( "z");

 
    wcmGUI::renderDateField('firstShowdate', date('Y-m-d'), _BIZ_SCHEDULES_FIRST_SHOWDATE);
    wcmGUI::renderDateField('lastshowDate',  date('Y-m-d'), _BIZ_SCHEDULES_LAST_SHOWDATE);

   
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    echo '</div>';
