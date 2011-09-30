<?php
/*
 * Project:     WCM
 * File:        account.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('business/account', array('class' => 'account'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
    //wcmGUI::renderObjectMenu();
/*
    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', false);
    $tabs->addTab('history', _HISTORY, true, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';
 */

    
    echo '<div id="content" style="margin-left:0px">';

	wcmGUI::openObjectForm($bizobject);

	$tabs = new wcmAjaxTabs('account', true);

	$userId = wcmSession::getInstance()->userId;
	$currentUser = new wcmUser();
	$currentUser->refresh($userId);
	$currentUserAccount = new account();
	$currentUserAccount->refreshByWcmUser($userId);
	
	if ($currentUser->isAdministrator||(($currentUserAccount->isManager() || $currentUserAccount->isChiefManager())&&($currentUserAccount->expirationDate >= date('Y-m-d') || $currentUserAccount->expirationDate == '')))
	{
		$tabs->addTab('t2', _MANAGEMENT, true, null, wcmModuleURL('business/ugc/account/management'));
	}
	$tabs->addTab('t1', _ACCOUNT, false, null, wcmModuleURL('business/ugc/account/properties'));
	if ($currentUser->isAdministrator)
	{
		$tabs->addTab('t3', _SPECIAL_OPERATIONS, false, null, wcmModuleURL('business/ugc/account/special'));
	}

    $tabs->render($bizobject->id === 0);
    wcmGUI::closeForm();
    echo '</div>';

    include(WCM_DIR . '/pages/includes/footer.php');
