<?php
/**
 * Project:     M
 * File:        modules/ugc/account/special.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

    $project = wcmProject::getInstance();
    $config = wcmConfig::getInstance();
    
    $userId = wcmSession::getInstance()->userId;
    
    $bizobject = wcmMVC_Action::getContext();
    $bizobject->refreshByWcmUser($userId);

    $sysobject = new wcmUser($project);
    if ($bizobject->wcmUserId)
    	$sysobject->refresh($bizobject->wcmUserId);

    echo '<div class="zone">';
	
 	wcmGUI::openCollapsablePane(_AFFECT_ACCOUNTS);
	wcmGUI::openFieldset('');
	echo "<div id='specialMessage'>&nbsp;</div>";
		$managerList = array();
		$currentAccount = new account();
		//$currentAccount->beginEnum('managerId is Not Null');
		$currentAccount->beginEnum("profile = '_ACCOUNT_PROFILE_MANAGER' or profile = '_ACCOUNT_PROFILE_SUPERVISOR' or profile = '_ACCOUNT_PROFILE_ADMINISTRATOR'");
		while ($currentAccount->nextEnum())
		{
			$currentAccount->refreshWcmUser();
			$managerList[$currentAccount->wcmUserId] = getConst($currentAccount->wcmUser_name).' (Manager)';
		}
		$currentAccount->endEnum();
		$currentUser = new wcmUser();
		$currentUser->beginEnum('isAdministrator=1');
		while ($currentUser->nextEnum())
		{
			if (!isset($managerList[$currentUser->id]))
				$managerList[$currentUser->id] = getConst($currentUser->name).' (wcmUser Administrator)';
		}
		$currentUser->endEnum();
		//
		$userList = array();
		//
		wcmGUI::renderDropdownField('managerIdFrom', $managerList, '', _INITIAL_MANAGER, array('onchange'=>'getAllUsers($(\'managerIdFrom\')[$(\'managerIdFrom\').selectedIndex].value);'));
		echo '<div id="mareponse"></div>';
		//wcmGUI::renderDropdownField('managerAccounts', $userList, '', _USERS, array('onchange'=>'ajaxAccount(\'transferUserToManager\', \'\', $(\'managerAccounts\')[$(\'managerAccounts\').selectedIndex].value, $(\'managerIdTo\')[$(\'managerIdTo\').selectedIndex].value, \'specialMessage\',\'\', \'\', \'\'); return false;'));
		wcmGUI::renderDropdownField('managerIdTo', $managerList, '', _NEW_MANAGER);
		wcmGUI::renderButton(_BIZ_SAVE,_BIZ_SAVE,array('onclick'=>'document.getElementById("specialMessage").innerHTML=""; ajaxAccount(\'transfer\', \'\', $(\'managerAccounts\')[$(\'managerAccounts\').selectedIndex].value, 0, $(\'managerIdFrom\')[$(\'managerIdFrom\').selectedIndex].value, $(\'managerIdTo\')[$(\'managerIdTo\').selectedIndex].value, \'specialMessage\',\'\', \'\', \'\'); return false;'));
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
    echo '</div>';
	echo "<script type='text/javascript' defer='defer'>";
	echo "	getAllUsers($('managerIdFrom')[$('managerIdFrom').selectedIndex].value);";
	echo "</script>";
