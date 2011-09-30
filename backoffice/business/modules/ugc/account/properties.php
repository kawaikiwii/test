<?php
/**
 * Project:     M
 * File:        modules/ugc/account/properties.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();

    $bizobject->refreshByWcmUser(wcmSession::getInstance()->userId);

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_GENERAL);
    if (!$bizobject->id)
    	echo '<b>'._NO_ACCOUNT.'</b><br/><br/>';
    wcmGUI::openFieldset(_ACCOUNT);
/*    wcmGUI::renderBooleanField('isManager', $bizobject->isManager, _IS_MANAGER, array('disabled' => 'disabled'));
    wcmGUI::renderBooleanField('isChiefManager', $bizobject->isChiefManager, _IS_CHIEFMANAGER, array('disabled' => 'disabled'));*/
    wcmGUI::renderTextField('profile', getConst($bizobject->profile), _DASHBOARD_MODULE_ACCOUNT_PROFILE, array('disabled' => 'disabled'));
    wcmGUI::renderTextField('expirationDate', $bizobject->expirationDate, _BIZ_EXPIRATIONDATE, array('disabled' => 'disabled'));
    wcmGUI::renderTextField('companyName', $bizobject->companyName, _BIZ_COMPANYNAME, array('disabled' => 'disabled'));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';