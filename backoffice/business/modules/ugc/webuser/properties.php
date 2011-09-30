<?php
/**
 * Project:     M
 * File:        modules/ugc/webuser/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_GENERAL);
    wcmGUI::openFieldset(_BIZ_WEBUSER);
    /* TODO missing id as output */
    /* TODO missing photoPicker */
    wcmGUI::renderTextField('email', $bizobject->email, _BIZ_EMAIL . ' *', array('class' => 'type-email-req'));
    wcmGUI::renderTextField('username', $bizobject->username, _BIZ_USERNAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderPasswordField('password', $bizobject->password, _BIZ_PASSWORD);
    wcmGUI::renderTextField('firstname', $bizobject->firstname, _BIZ_FIRSTNAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('lastname', $bizobject->lastname, _BIZ_LASTNAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('address', $bizobject->address, _BIZ_ADDRESS);
    wcmGUI::renderTextField('postalCode', $bizobject->postalCode, _BIZ_POSTALCODE);
    wcmGUI::renderTextField('city', $bizobject->city, _BIZ_CITY);
    wcmGUI::renderTextField('state', $bizobject->state, _BIZ_STATE_PROVINCE);
    wcmGUI::renderTextField('country', $bizobject->country, _BIZ_COUNTRY);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';