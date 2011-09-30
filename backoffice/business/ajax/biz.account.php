<?php

/**
 * Project:     WCM
 * File:        biz.account.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve parameters
$command		= getArrayParameter($_REQUEST, "command", null);
$type			= getArrayParameter($_REQUEST, "type", "childs");
$wcmUserId		= getArrayParameter($_REQUEST, "wcmUserId", 0);
$itemId			= getArrayParameter($_REQUEST, "itemId", 0);
$managerIdFrom	= getArrayParameter($_REQUEST, "managerIdFrom", 0);
$managerIdTo	= getArrayParameter($_REQUEST, "managerIdTo", 0);
$divId			= getArrayParameter($_REQUEST, "divId", 0);
$formDatas		= getArrayParameter($_REQUEST, "formDatas", null);
$fullText		= getArrayParameter($_REQUEST, "fullText", '');
$orderBy		= getArrayParameter($_REQUEST, "orderBy", 'id DESC');
$lang			= $_SESSION['wcmSession']->getLanguage();
$hideInactive   = getArrayParameter($_REQUEST, "hideInactive", 1);

if ($command == 'getAllUsers')
{
	$managerId	= getArrayParameter($_REQUEST, "managerId", '1');
	$managerAccount = new account();
	$userIds = account::getAccountsIds($managerId);
	$users = array();
	if( sizeof($userIds) > 0) {
		$users[0] = "---------- ALL USERS --------------------------------------------------------------------------------";
	} else {
		$users[0] = " { No user } ";
	}
	
	foreach($userIds as $userId)
	{
		$managerAccount->refresh($userId);
		$users[$managerAccount->wcmUserId] = $managerAccount->__get('wcmUser_name');
	}
	
	// header
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
	header("Content-Type: text/xml");
	
	// Response
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo "<ajax-response>\n";
	echo "<response type=\"item\" id=\"mareponse\"><![CDATA[";
	//echo "<response type=\"item\" id=\"managerAccounts\">";
	/*foreach($userIds as $userId)
	{
		$managerAccount->refresh($userId);
		echo '<option value="'.$userId.'">'.$managerAccount->__get('wcmUser_name').'</option>';
	}*/
	wcmGUI::renderDropdownField('managerAccounts', $users, '', _USERS);
	echo "]]></response>";
	echo "</ajax-response>";
	
	exit();
}


$msg = '';

$formsDatasArray = array();
if ($formDatas)
{
	$groups = array();
	$subscriptions = array();
	$subscriptions_on = array();
	$temp = explode('&',$formDatas);
	
	//print_r($temp);
	
	foreach ($temp as $item)
	{
		$temp2 = explode('=',$item);
		$formsDatasArray[urldecode($temp2[0])] = urldecode($temp2[1]);
		if (substr(urldecode($temp2[0]), 0, 8) == '_groups[')
		{
			$length = strpos(urldecode($temp2[0]), ']') - strpos(urldecode($temp2[0]), '[');
			$groups[substr(urldecode($temp2[0]), 8, $length-1)] = urldecode($temp2[1]);
		}
		if (substr(urldecode($temp2[0]), 0, 15) == '_subscriptions[')
		{
			$length = strpos(urldecode($temp2[0]), ']') - strpos(urldecode($temp2[0]), '[');
			$subscriptions[substr(urldecode($temp2[0]), 15, $length-1)] = urldecode($temp2[1]);
		}
		if (substr(urldecode($temp2[0]), 0, 22) == '_wcmBox_subscriptions[')
		{
			$length = strpos(urldecode($temp2[0]), ']') - strpos(urldecode($temp2[0]), '[');
			$subscriptions_on[substr(urldecode($temp2[0]), 22, $length-1)] = urldecode($temp2[1]);
		}
		
	}
}



if ($command == 'insert' || $command =='update')
{
	if ($formsDatasArray['password'] == '')
	{
		$divId = "errorMsg";
		$msg = _PASWORD_MANDATORY;
	}
	else
	{
		$userId = wcmSession::getInstance()->userId;
		$managerAccount = new account();
		$managerAccount->refreshByWcmUser($userId);
		if ($managerAccount->id)
			if ($managerAccount->expirationDate != NULL && $formsDatasArray['expirationDate'] > $managerAccount->expirationDate)
			{
				$divId = "errorMsg";
				$msg = _ACCOUNT_INVALID_SUP_DATE_MANAGER.' ('.$managerAccount->expirationDate.')';
			}
		if (($formsDatasArray['profile'] == '_ACCOUNT_PROFILE_CLIENT' || $formsDatasArray['profile'] == '_ACCOUNT_PROFILE_MANAGER' || $formsDatasArray['profile'] == '_ACCOUNT_PROFILE_DEMO') && $formsDatasArray['expirationDate'] == NULL)
		{
			$divId = "errorMsg";
			$msg = _ACCOUNT_INVALID_NULL_DATE;
		}
	}
}

if ($msg == '')
{
	switch($command)
	{
		case "insert":
			$wcmUser = new wcmUser();
			$wcmUser->bind($formsDatasArray);
			if (isset($groups) && !empty($groups))
			{
				foreach ($groups as $id => $active)
				{
					if ($active) $groups[] = $id;
				}				
				$wcmUser->setGroups($groups);
			}
			//If we cannot save the sysuser we don't save the account and alert save failed
			if(!$wcmUser->save())
			{
				$divId = "errorMsg";
				$msg = $wcmUser->getErrorMsg();
			}
			else
			{	
				$flush = fopen(WCM_DIR.'/business/xml/dashboard/user-'.$wcmUser->id.'.xml','a');
				fputs($flush,"<?xml version=\"1.0\" encoding=\"utf-8\" ?><dashboard><literal><![CDATA[]]></literal></dashboard>");
				fclose($flush);
				$account = new account();
				$account->bind($formsDatasArray);
				$account->wcmUserId = $wcmUser->id;
								
				$account->save();
				
				// init permission from ancestor
				if (empty($account->getArrayPermissions))
				{
					$ancestorAccount = new account();
					$ancestorAccount->refreshByWcmUser($account->managerId);
					$permissionArray = $ancestorAccount->getArrayPermissions();
					
					// duplicate ancestor permissions except if managerId is root
					if (!empty($permissionArray) && $account->managerId != 1)
					{
						$account->setPermissionsFromArray($permissionArray);
					}
				}
				
				
				$newsletters = newsletter::getBizobjects("newsletter"); 
				$aNewsletters = array();
				foreach($newsletters as $newsletter) {
					$aNewsletters[$newsletter->id] = $newsletter->code;
				}
				
				$subscribeds = $account->getSubscriptions('newsletter');
				$aSubscribed = array();
				foreach ($subscribeds as $subscribed) {
					$aSubscribed[$subscribed->subscribedId] = $subscribed->id;
				}
				
				foreach ($subscriptions as $id => $active)
				{
					if (array_key_exists($id, $subscriptions_on)) 
					{
						if (!array_key_exists($id, $aSubscribed)) {
						 	$subscription = new subscription;
						    $subscription->sysUserId = $wcmUser->id;;
						    $subscription->subscribedId = $id;
						    $subscription->subscribedClass = "newsletter";
						    $subscription->subscriptionStart = "2009-02-02 00:00:00";
						    $subscription->subscriptionEnd = "2099-02-02 00:00:00";
							$subscription->exportRuleCode = $aNewsletters[$id];
						    $subscription->save();
							}
					} else {
						if (array_key_exists($id, $aSubscribed)) {
						 	$subscription = new subscription($aSubscribed[$id]);
							$subscription->refresh($aSubscribed[$id]);
							$subscription->delete();
							}
					}
				}		
			}
			break;

		case "update":
			$wcmUser = new wcmUser();			
			$wcmUser->refresh($formsDatasArray['wcmUserId']);			
			$wcmUser->bind($formsDatasArray);		
			/*
			if (isset($groups) && !empty($groups))
			{
				foreach ($groups as $id => $active)
				{
					if ($active) $groups[] = $id;
				}
				
				$wcmUser->setGroups($groups);
			}
			*/
			//If we cannot save the sysuser we don't save the account and alert save failed
			if(!$wcmUser->save())
			{
				$divId = "errorMsg";
				$msg = $wcmUser->getErrorMsg();
			}
			else
			{
				
				$account = new account();			
				$account->refresh($itemId);
				$account->bind($formsDatasArray);
				$account->save();
				
				$newsletters = newsletter::getBizobjects("newsletter"); 
				$aNewsletters = array();
				foreach($newsletters as $newsletter) {
					$aNewsletters[$newsletter->id] = $newsletter->code;
				}
				
				$subscribeds = $account->getSubscriptions('newsletter');
				$aSubscribed = array();
				foreach ($subscribeds as $subscribed) {
					$aSubscribed[$subscribed->subscribedId] = $subscribed->id;
				}
				
				if (!empty($subscriptions))
				{
					foreach ($subscriptions as $id => $active)
					{
						if (array_key_exists($id, $subscriptions_on)) 
						{
							if (!array_key_exists($id, $aSubscribed)) {
							 	$subscription = new subscription;
							    $subscription->sysUserId = $wcmUser->id;;
							    $subscription->subscribedId = $id;
							    $subscription->subscribedClass = "newsletter";
							    $subscription->subscriptionStart = "2009-02-02 00:00:00";
							    $subscription->subscriptionEnd = "2099-02-02 00:00:00";
								$subscription->exportRuleCode = $aNewsletters[$id];
							    $subscription->save();
								}
						} else {
							if (array_key_exists($id, $aSubscribed)) {
							 	$subscription = new subscription($aSubscribed[$id]);
								$subscription->refresh($aSubscribed[$id]);
								$subscription->delete();
								}
						}
					}
				}
				
					
			}
			break;

		case "delete":
			$account = new account();
			$account->refresh($itemId);
			$account->delete();
			break;
			
		case "transfer":
			account::transferManager($wcmUserId, $managerIdFrom, $managerIdTo);
			$msg = _TRANSFER_DONE;
			break;
	}
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Retrieve related accounts
if (empty($orderBy)) $orderBy = "id DESC";
$accounts = account::getAccounts($wcmUserId,$type,$fullText,$orderBy,$hideInactive);

//$accounts = array();

// Write ajax response
echo "<ajax-response>\n";
echo "<response type='item' id='".$divId."'><![CDATA[";

if ($msg != '')
	echo "<div style=\"background-color:red; text-align: center; font-weight:bolder;\">".$msg."</div>";
else
{
	echo "<ul class='filters'>";
	echo "  <li><a href=\"javascript:divAccountWait(); ajaxAccountSpe('refresh', 'childs', '".$wcmUserId."', 0, 0, 0, '".$divId."','', '".$fullText."', '".$orderBy."', document.getElementById('hideInactive').value);\" ";
		if ($type == 'childs')
			echo "class='selected'";
		echo ">"._CHILDS."</a></li>";
	echo "  <li><a href=\"javascript:divAccountWait(); ajaxAccountSpe('refresh', 'family', '".$wcmUserId."', 0, 0, 0, '".$divId."','', '".$fullText."', '".$orderBy."', document.getElementById('hideInactive').value);\" ";
		if ($type == 'family')
			echo "class='selected'";
		echo ">"._FAMILY."</a></li>";
	$sysobject = new wcmUser($project);
	$sysobject->refresh(wcmSession::getInstance()->userId);
	if ($sysobject->isAdministrator)
	{
	echo "  <li><a href=\"javascript:divAccountWait(); ajaxAccountSpe('refresh', 'all', '".$wcmUserId."', 0, 0, 0, '".$divId."','', '".$fullText."', '".$orderBy."', document.getElementById('hideInactive').value);\" ";
		if ($type == 'all')
			echo "class='selected'";
		echo ">"._ALL."</a></li>";
	}
	echo "</ul>";
	echo "<div style='align:right'>";
	echo "<div id='accountSummary' name='accountSummary' style='float:right;display:inline-block;width:200px;text-align:center;margin-right:250px;font-weight:bold;'>" . count($accounts) . " " ._USERS . "</div>";
	if ($hideInactive == 1)
	{
		$option1 = "selected";
		$option2 = "";
	}
	else 
	{
		$option1 = "";
		$option2 = "selected";
	}
	echo "&nbsp;<select name='hideInactive' id='hideInactive' onChange=\"divAccountWait(); ajaxAccountSpe('refresh', '".$type."', '".$wcmUserId."', 0, 0, 0, '".$divId."','', document.getElementById('fullText').value, '".$orderBy."',document.getElementById('hideInactive').value);\"><option value='1' ".$option1.">Hide inactive acounts</option><option value='0'  ".$option2.">Show inactive acounts</option></select>&nbsp;";
	//echo "&nbsp;<input type='checkbox' id='hideInactive' name='hideInactive' value='1' onClick=\"divAccountWait(); ajaxAccount('refresh', '".$type."', '".$wcmUserId."', 0, 0, 0, '".$divId."','', document.getElementById('fullText').value, '".$orderBy."', document.getElementById('hideInactive').value);\"/>&nbsp; Hide inactive accounts&nbsp;";
	// forbidden enter key pressed 
	echo "<input type='text' id='fullText' value='".$fullText."' onkeypress='return event.keyCode!=13'>";
	echo "<input type='button' value='"._SEARCH."'  onClick=\"divAccountWait(); ajaxAccountSpe('refresh', '".$type."', '".$wcmUserId."', 0, 0, 0, '".$divId."','', document.getElementById('fullText').value, '".$orderBy."', document.getElementById('hideInactive').value);\">";

	echo "<div class='wait' style='display:none'>Loading...</div>";
	echo "</div>";
	echo "<table id='accounts'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th width='30'>&nbsp;</th>";
	echo "<th width='30'>&nbsp;</th>";

	
	echo "<th>"._DASHBOARD_MODULE_ACCOUNT_NAME."</th>";

	/*$orderByName = ($orderBy == 'wcmUserId ASC') ? 'wcmUserId DESC' : 'wcmUserId ASC';
	echo "<th><a href=\"javascript:divWait(); ajaxAccount('refresh', '".$type."', '".$wcmUserId."', 0, 0, '".$divId."','', '".$fullText."', '".$orderByName."');\";>"._DASHBOARD_MODULE_ACCOUNT_NAME."</a>";
	if ($orderBy == 'wcmUserId ASC')
		echo "<img src='img/search/arrow-down.gif'>";
	elseif ($orderBy == 'wcmUserId DESC')
		echo "<img src='img/search/arrow-up.gif'>";
	else
		echo "<img src='img/empty.gif' width='13' height='13'>";
	echo "</th>";*/
	
	echo "<th>Login</th>";
	/*
	$orderByLogin = ($orderBy == 'login ASC') ? 'login DESC' : 'login ASC';
	echo "<th><a href=\"javascript:divWait(); ajaxAccount('refresh', '".$type."', '".$wcmUserId."', 0, 0, '".$divId."','', '".$fullText."', '".$orderByLogin."');\";>Login</a>";
	if ($orderBy == 'login ASC')
		echo "<img src='img/search/arrow-down.gif'>";
	elseif ($orderBy == 'login DESC')
		echo "<img src='img/search/arrow-up.gif'>";
	else
		echo "<img src='img/empty.gif' width='13' height='13'>";
	echo "</th>";
	*/
	
	//echo "<th>"._DASHBOARD_MODULE_ACCOUNT_COMPANYNAME."</th>";

	$orderByCompagny = ($orderBy == 'companyName ASC') ? 'companyName DESC' : 'companyName ASC';
	echo "<th><a href=\"javascript:divAccountWait(); ajaxAccount('refresh', '".$type."', '".$wcmUserId."', 0, 0, 0, '".$divId."','', '".$fullText."', '".$orderByCompagny."');\";>"._DASHBOARD_MODULE_ACCOUNT_COMPANYNAME."</a>";
	if ($orderBy == 'companyName ASC')
		echo "<img src='img/search/arrow-down.gif'>";
	elseif ($orderBy == 'companyName DESC')
		echo "<img src='img/search/arrow-up.gif'>";
	else
		echo "<img src='img/empty.gif' width='13' height='13'>";
	echo "</th>";
	
	echo "<th>"._DASHBOARD_MODULE_ACCOUNT_MANAGERNAME."</th>";

	/*$orderByManager = ($orderBy == 'managerId ASC') ? 'managerId DESC' : 'managerId ASC';
	echo "<th><a href=\"javascript:divWait(); ajaxAccount('refresh', '".$type."', '".$wcmUserId."', 0, 0, 0, '".$divId."','', '".$fullText."', '".$orderByManager."');\";>"._DASHBOARD_MODULE_ACCOUNT_MANAGERNAME."</a>";
	if ($orderBy == 'managerId ASC')
		echo "<img src='img/search/arrow-down.gif'>";
	elseif ($orderBy == 'managerId DESC')
		echo "<img src='img/search/arrow-up.gif'>";
	else
		echo "<img src='img/empty.gif' width='13' height='13'>";
	echo "</th>";*/

//	echo "<th>"._DASHBOARD_MODULE_ACCOUNT_STARTINGDATE."</th>";
	
	$orderByExpirationDate = ($orderBy == 'expirationDate ASC') ? 'expirationDate DESC' : 'expirationDate ASC';
	echo "<th width='88'><a href=\"javascript:divAccountWait(); ajaxAccount('refresh', '".$type."', '".$wcmUserId."', 0, 0, 0, '".$divId."','', '".$fullText."', '".$orderByExpirationDate."');\";>"._DASHBOARD_MODULE_ACCOUNT_EXPIRATIONDATE."</a>";
	if ($orderBy == 'expirationDate ASC')
		echo "<img src='img/search/arrow-down.gif'>";
	elseif ($orderBy == 'expirationDate DESC')
		echo "<img src='img/search/arrow-up.gif'>";
	else
		echo "<img src='img/empty.gif' width='13' height='13'>";
	echo "</th>";

	$orderByProfile = ($orderBy == 'profile ASC') ? 'profile DESC' : 'profile ASC';
//	echo "<th><a href=\"javascript:divWait(); setTimeout(ajaxAccount('refresh', '".$type."', '".$wcmUserId."', 0, 0, 0, '".$divId."','', '".$fullText."', '".$orderByProfile."'), 500);\";>"._DASHBOARD_MODULE_ACCOUNT_PROFILE."</a>";
	echo "<th><a href=\"javascript:ajaxAccount('refresh', '".$type."', '".$wcmUserId."', 0, 0, 0, '".$divId."','', '".$fullText."', '".$orderByProfile."');\";>"._DASHBOARD_MODULE_ACCOUNT_PROFILE."</a>";
	if ($orderBy == 'profile ASC')
		echo "<img src='img/search/arrow-down.gif'>";
	elseif ($orderBy == 'profile DESC')
		echo "<img src='img/search/arrow-up.gif'>";
	else
		echo "<img src='img/empty.gif' width='13' height='13'>";
	echo "</th>";
/*
	echo "<th>"._DASHBOARD_MODULE_ACCOUNT_IS_MANAGER."</th>";
	echo "<th>"._DASHBOARD_MODULE_ACCOUNT_IS_CHIEFMANAGER."</th>";
*/
	echo "<th colspan='2'>"._DASHBOARD_MODULE_ACCOUNT_STATISTICS."</th>";
	echo "<th><a href=\"#\" onClick=\"javascript:toggleCheckboxes('checkbox_account_'); return false;\">"._TOGGLE."</a></th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	if (count($accounts)>0)
	{
		$i=0;
		$managerAccount = new account();
		$managerAccount->refreshByWcmUser($wcmUserId);
		foreach ($accounts as $current_account)
		{
			if ($i%2==0)
				echo "<tr id='account_".$current_account->id."' class=\"off\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\">";
			else
				echo "<tr id='account_".$current_account->id."' class=\"off2\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off2'\">";

			echo "<td class='actions'>";
			
			    //if ($current_account->managerId != $wcmUserId || (!in_array($current_account->managerId, account::getAccountsIds($wcmUserId, "family"))))
				//if ($current_account->managerId != $wcmUserId)
				//	echo '&nbsp;';
				//else
				//{
					echo "<ul class='two-buttons' style='margin-left:8px'>";
						echo "<li><a class='edit' title='"._EDIT."' href=\"javascript:openmodal('"._UPDATE_ACCOUNT."','800'); modalPopup('account','update', '".$current_account->id."', '".$fullText."', '".$current_account->wcmUserId."', '".$type."', '".$orderBy."');\"><span>"._EDIT."</span></a></li>";
						if ($current_account->nbChilds())
							echo "<li><a class='delete' title='"._DELETE."' href=\"javascript: alert('"._NO_DELETE_FAMILY."');\" id=''><span>"._DELETE."</span></a></li>";
						else
							echo "<li><a class='delete' title='"._DELETE."' href=\"javascript: if (confirm('"._ACCOUNT_DELETE_CONFIRM."')) (ajaxAccount('delete', '".$type."', '".$wcmUserId."', '".$current_account->id."', 0, 0, '".$divId."','', '".$fullText."', '".$orderBy."'));\" id=''><span>"._DELETE."</span></a></li>";
					echo "</ul>";
				//}
			echo "</td>";
			
			echo "<td class='actions'>";
				//if ($current_account->managerId != $wcmUserId)
				//	echo '&nbsp;';
				//else
				//{
					//echo "<ul class='two-buttons' style='margin-left:8px;'>";
					// test parent permissions
					$ppermissions = new account();
					$ppermissions->refreshByWcmUser($current_account->managerId);
					if ($ppermissions->hasPermissions)
					{
						$wcmUserAccount = new wcmUser();
						$wcmUserAccount->refresh($current_account->wcmUserId);
						//echo "<ul class='one-buttons' style='margin-left:8px;'>";
						if($managerAccount->profile == "_ACCOUNT_PROFILE_SUPERVISOR" && $current_account->hasPermissions && !empty($wcmUserAccount->email) && !empty($wcmUserAccount->token)){
							echo "<ul class='three-buttons' style='margin-left:8px;'>";
							echo "<li><a class='sendmdp' title='"._SEND_PASSWORD."' href=\"javascript:if(confirm('"._CONFIRM_SEND_PASSWORD."')){openmodal('"._SEND_PASSWORD."','300', '200'); modalPopup('sendmdp','send','".$current_account->id."');}\" id=''><span>Envoyer mot de passe</span></a></li>";
						}
						else
							echo "<ul class='two-buttons' style='margin-left:8px;'>";
						$permissionsHightlight = ($current_account->hasPermissions) ? '' :  " style='background-color:#BA3131'";
						//echo "<li><a class='permission'".$permissionsHightlight." title='"._PERMISSIONS."' href=\"javascript:openmodal('"._PERMISSIONS."','1100', '450'); modalPopup('account','permissions', '".$current_account->id."', '".$current_account->wcmUserId."', 0, 0, '');\" id=''><span>"._PERMISSIONS."</span></a></li>";
						echo "<li><a class='permission'".$permissionsHightlight." title='"._PERMISSIONS."' href=\"javascript:openmodal('"._PERMISSIONS."','700', '550'); modalPopup('permissions','permissions', '".$current_account->id."', '".$current_account->wcmUserId."', 0, 0, '');\" id=''><span>Perm V2</span></a></li>";
						// ADD ALERT MODALBOX
						echo "<li><a class='alerte' title='"._PM_ALERT."' href=\"javascript:openmodal('"._PM_ALERT."','1100'); modalPopup('alerte','refresh','".$current_account->id."');\"><span>ALERTE</span></a></li>";
						echo "</ul>";
					}
				//}
			echo "</td>";
			
			echo "<td align='center'>";
			
// Modif LSJ 21/04/2009
// Scission NOM Prénom pour les users
			$arrayName = explode("|", $current_account->wcmUser_name);
			if(count($arrayName) > 1) {
				$firstname = $arrayName[0];
				$lastname = $arrayName[1];
			} else {
				$firstname = "";
				$lastname = $arrayName[0];
			}
			
				//echo $current_account->wcmUser_name;
				echo $firstname . " " . $lastname;
// Fin modif
			echo "</td>";
			echo "<td align='center'>";
				echo $current_account->wcmUser_login;
			echo "</td>";
			echo "<td align='center'>";
				echo $current_account->companyName;
			echo "</td>";
			echo "<td align='center'>";
				echo $current_account->manager_name;
			echo "</td>";
/*
			echo "<td align='center'>";
				if ($current_account->startingDate > date('Y-m-d'))
					echo $current_account->startingDate;
				else
					echo '<span style="background-color:orange">'.$current_account->startingDate.'</span>';
			echo "</td>";
*/
			echo "<td align='center'>";
				if ($current_account->expirationDate >= date('Y-m-d'))
					echo $current_account->expirationDate;
				else
					echo '<span style="background-color:orange">'.$current_account->expirationDate.'</span>';
			echo "</td>";
			echo "<td align='center'>";
				echo getConst($current_account->profile);
				
			echo "</td>";
/*
 * 			echo "<td align='center'>";
				if ($current_account->isManager)
					echo "<img src=img/grant.gif>";
				else
					echo "<img src=img/deny.gif>";
			echo "</td>";
			echo "<td align='center'>";
				if ($current_account->isChiefManager)
					echo "<img src=img/grant.gif>";
				else
					echo "<img src=img/deny.gif>";
			echo "</td>";
*/
			$urlPop = "index-stats.php?lang=".$lang."&id=".$current_account->wcmUserId;
			$arguments = "'height=600,width=850,top=150,left='+(screen.width-850)/2+',scrollbars=yes,location=no,status=no,menubar=no,resizable=no,titlebar=no,toolbar=no,fullscreen=no'";
			echo "<td align='center' width='20'>";
			echo "<a href=\"javascript:void(0)\" title=\"" . BIZ_ACCOUNT_STATISTICS_SHOW . "\" onClick=\"window.open('" . $urlPop . "','statsUser', " . $arguments . ");\" /><img src=\"../../skins/default/images/bizobjects/generic.png\" border=\"0\" /></a>";
			echo "</td>";
			echo "<td align='center' width='20'>";
				if ($current_account->nbChilds() > 0) {
					$urlPop = "index-stats.php?lang=".$lang."&managerId=".$current_account->wcmUserId;
					echo "<a href=\"javascript:void(0)\" title=\"" . BIZ_ACCOUNT_CHILDS_STATISTICS_SHOW . $firstname . " " . $lastname . "\" onClick=\"window.open('" . $urlPop . "','statsManager', " 
					. $arguments . ");\" /><img src=\"../../skins/default/images/bizobjects/manager_page.png\" border=\"0\" /></a>";
				} else {
					echo "&nbsp;";
				}
			echo "</td>";
			echo "<td>";
			echo "<input style=\"display:inline\" type=\"checkbox\" id=\"checkbox_account_".$current_account->id."\" onClick=\"manageArrayAccount('checkbox_account_".$current_account->id."')\" />";
			echo "</td>";
			echo "</tr>";
			$i++;
		}
	}
	else
	{
		echo "<tr><td colspan='9'> - ("._EMPTY.") - </td></tr>";
	}
	echo "</tbody>";
	echo "</table>";
}
echo "]]></response>\n";
echo "</ajax-response>";

?>
