<?php
/**
 * Project:     WCM
 * File:        modules/editorial/newsletter/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();

    echo '<div class="zone">';
 

    wcmGUI::openCollapsablePane(_BIZ_NEWSLETTER_CONFIGURATION);
    wcmGUI::openFieldset();
    wcmGUI::renderTextField('code', $bizobject->code, _BIZ_CODE . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('sender', $bizobject->sender, _BIZ_SENDER . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('from', $bizobject->from, _BIZ_FROM . ' *', array('class' => 'type-email-req'));
    wcmGUI::renderTextField('replyTo', $bizobject->replyTo, _BIZ_REPLY_TO, array('class' => 'type-email'));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    echo '</div>';
