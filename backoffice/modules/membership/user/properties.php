<?php
/**
 * Project:     WCM
 * File:        modules/membership/user/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $project = wcmProject::getInstance();
    $config  = wcmConfig::getInstance();
    $sysobject = wcmMVC_Action::getContext();
    $arrGmt= array();
     
    for($i=-12;$i<=14;$i++)
    {
      if(substr($i,0,1)=='-')
        $arrGmt[$i] = "UTC/GMT ".$i;
      else
        $arrGmt[$i] = "UTC/GMT +".$i;
    }
    
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_PROPERTIES);
    wcmGUI::openFieldset( _GENERAL);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('login', $sysobject->login, _LOGIN . ' *', array('class' => 'type-req'));
    wcmGUI::renderPasswordField('password', $sysobject->password, _PASSWORD . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('email', $sysobject->email, _EMAIL, array('class' => 'type-email'));
    wcmGUI::renderDropdownField('timezone', $arrGmt, $sysobject->timezone, _TIMEZONE);
    wcmGUI::renderBooleanField('isAdministrator', $sysobject->isAdministrator, _IS_ADMINISTRATOR);
    wcmGUI::closeFieldset();

	$sysobject->refresh();
    // Render member list
    wcmGUI::openFieldset(_GROUPS);
    foreach($project->membership->getGroups() as $group)
    {
        // Ignore everyone group
        if ($group->id != wcmMembership::EVERYONE_GROUP_ID)
        {
            $selected = ($sysobject->isMemberOf($group->id) || (isset($params['groupId']) && ($params['groupId'] == $group->id)) );
            wcmGUI::renderBooleanField('_groups['.$group->id . ']', $selected, getConst($group->name));
        }
    }
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';
