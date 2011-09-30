<?php
/**
 * Project:     WCM
 * File:        modules/membership/group/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $sysobject = wcmMVC_Action::getContext();

    // Special actions
    $info  = '';
    if ($sysobject->id)
    {
        $info .= '<ul class="actions">';
        $info .= '<li><a href="'. wcmMVC_SysAction::computeObjectURL('wcmuser', 0, 'view', array('groupId' => $sysobject->id));
        $info .= '">' . _NEW_USER . '</a></li>';
        $info .= '</ul>';
    }
    
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_PROPERTIES, true, $info);
    wcmGUI::openFieldset( _GENERAL);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::closeFieldset();

    // Render member list
    wcmGUI::openFieldset(_MEMBERS);
    // @todo ::     wcmGUI::renderTextField('user_addnew', null, ' ');
    $attributes = null;
    if ($sysobject->id == wcmMembership::EVERYONE_GROUP_ID)
        $attributes = array('disabled' => 'disabled');
    echo "<li><a href=\"javascript:checkUncheckAll('wcmTabs_group_wcmTab_t1');\">tout cocher/décocher</a></li>";
    foreach($sysobject->getMembers() as $user)
    {
        wcmGUI::renderBooleanField('_users['.$user->id . ']', true, getConst($user->name), $attributes);
    }
    wcmGUI::closeFieldset();
    
    wcmGUI::closeCollapsablePane();
    echo '</div>';