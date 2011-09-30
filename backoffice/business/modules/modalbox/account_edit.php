<?php 
/**
 * Project:     WCM
 * File:        account_edit.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */
 
require_once dirname(__FILE__).'/../../../initWebApp.php';

$config = wcmConfig::getInstance();

$id = getArrayParameter($_REQUEST, "id", 0);
$action = getArrayParameter($_REQUEST, "kind", null);
$targetid = getArrayParameter($_REQUEST, "targetid", '');
$fullText = getArrayParameter($_REQUEST, "input", null);
$type = getArrayParameter($_REQUEST, "type", 'childs');
$order = getArrayParameter($_REQUEST, "order", null);

$userId = wcmSession::getInstance()->userId;

$currentUser = new wcmUser();
$currentUser->refresh($userId);
$currentUserAccount = new account();
$currentUserAccount->refreshByWcmUser($userId);

if (!empty($id)) $_SESSION["perm_user_id"] = $id;
else $_SESSION["perm_user_id"] = "";

$bizobject = new account();
if ($id)
    $bizobject->refresh($id);
else {
    $bizobject->expirationDate = date('Y-m-d');
}
$sysobject = new wcmUser();
if ($bizobject->wcmUserId)
    $sysobject->refresh($bizobject->wcmUserId);
    
$managerId = ($bizobject->managerId) ? $bizobject->managerId : $userId;
$wcmUserId = ($bizobject->wcmUserId) ? $bizobject->wcmUserId : 0;

switch ($action) {
    case 'insert':
    case 'update':
        echo "<div id=\"errorMsg\"></div>";
        echo '<div id="account">';
        echo "<form id='account_edit' name='account_edit'>";
        echo "<table border='0' width='98%'>";
        echo "<tr>";
        echo "<td valign='top'>";
        wcmGUI::renderHiddenField('managerId', $managerId);
        wcmGUI::renderHiddenField('wcmUserId', $wcmUserId);
        wcmGUI::openFieldset(_USER);
        echo '&nbsp;</br>';
        $arrGmt = array();
        for ($i = -12; $i <= 14; $i++) {
            if (substr($i, 0, 1) == '-')
                $arrGmt[$i] = "UTC/GMT ".$i;
            else
                $arrGmt[$i] = "UTC/GMT +".$i;
        }
        // Modif LSJ 21/04/2009
        // Scission NOM Prenom pour les users
        $arrayName = explode("|", $sysobject->name);
        if (count($arrayName) > 1) {
            $firstname = $arrayName[0];
            $lastname = $arrayName[1];
        } else {
            $firstname = "";
            $lastname = $arrayName[0];
        }
        wcmGUI::renderHiddenField('name', $sysobject->name);
        //wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
        wcmGUI::renderTextField('firstname', $firstname, 'first name'.' *', array('class'=>'type-req', 'style'=>'text-transform:capitalize'));
        wcmGUI::renderTextField('lastname', $lastname, 'last name'.' *', array('class'=>'type-req', 'style'=>'text-transform:uppercase'));
        // Fin modif
        wcmGUI::renderTextField('login', $sysobject->login, _LOGIN.' *', array('class'=>'type-req'));
        if(!empty($sysobject->password) && empty($sysobject->token))
        	wcmGUI::renderPasswordField('password', $sysobject->password, _PASSWORD.' *', array('class'=>'type-req'));
        else
        	wcmGUI::renderTextField('password', base64_decode($sysobject->token), _PASSWORD.' *', array('class'=>'type-req'));
        wcmGUI::renderTextField('email', $sysobject->email, _EMAIL, array('class'=>'type-email'));
        wcmGUI::renderDropdownField('timezone', $arrGmt, $sysobject->timezone, _TIMEZONE);
        echo '&nbsp;</br>';
        wcmGUI::closeFieldset();
        echo "</td>";
        echo "<td valign='top' rowspan='2'>";
        wcmGUI::openFieldset("Newsletter");
        echo '&nbsp;</br>';
        echo "<div style='width:250px; overflow:auto; border:1px solid grey; padding:5px;'>";
        //echo "...Not used yet...";
        
        $newsletters = newsletter::getBizobjects("newsletter");
        $aNewsletters = array();
        foreach ($newsletters as $newsletter) {
            $aNewsletters[$newsletter->id] = $newsletter->title;
        }
        
        $subscriptions = $bizobject->getSubscriptions('newsletter');
        $aSubscriptions = array();
        foreach ($subscriptions as $subscription) {
            $aSubscriptions[] = $subscription->subscribedId;
        }
        
        foreach ($aNewsletters as $newsletter=>$label) {
            wcmGUI::renderBooleanField("_subscriptions[$newsletter]", in_array($newsletter, $aSubscriptions));
            echo "<label>$label</label>";
        }
        
        /*
         if ($currentUser->isAdministrator)
         {
         $project = wcmProject::getInstance();
         $groups = $project->membership->getGroups();
         $groupList = array();
         foreach($groups as $group)
         {
         if ($group->id != wcmMembership::EVERYONE_GROUP_ID)
         $groupList[$group->id] = $group->id;
         }
         }
         else
         $groupList = $currentUser->getGroups();
         foreach($groupList as $groupId)
         {
         $group = new wcmGroup();
         $group->refresh($groupId);
         // Ignore everyone group
         if ($group->id != wcmMembership::EVERYONE_GROUP_ID)
         {
         $selected = '';
         if ($sysobject->id)
         $selected = ($sysobject->isMemberOf($group->id) || (isset($params['groupId']) && ($params['groupId'] == $group->id)) );
         wcmGUI::renderBooleanField('_groups['.$group->id . ']', $selected);
         echo "<label>".textH8(getConst($group->name))."</label>";
         }
         }
         */
        
        //echo $bizobject->wcmUserId;
        
        echo "</div>";
        wcmGUI::closeFieldset();
        echo "<br />";
        echo "<ul class='toolbar'>";
        echo "<li><a href='#' onclick=\"closemodal(); return false;\" class='cancel'>"._BIZ_CANCEL."</a></li>";
        //echo "<li><a href='#' onclick=\"document.getElementById('errorMsg').innerHTML = '';parent.ajaxAccount('".$action."', 'family', ".$id.", ".$wcmUserId.", 0, 0, 'results', $('account_edit').serialize(),'',''); if (document.getElementById('errorMsg').innerHTML == '') closemodal(); return false;\" class='save'>"._BIZ_SAVE."</a></li>";
        // Modif LSJ 21/04/2009
        // Scission NOM Prenom pour les users : ajout fonction js "build_name" dans account_edit.js
        echo "<li><a href='#' onclick=\"document.getElementById('errorMsg').innerHTML = ''; build_name(); parent.ajaxAccount('".$action."', '".$type."', ".$userId.", ".$id.", 0, 0, 'results', $('account_edit').serialize(),'".$fullText."','".$order."'); if (document.getElementById('errorMsg').innerHTML == '') closemodal(); return false;\" class='save'>"._BIZ_SAVE."</a></li>";
        // Fin modif
        echo "</ul>";
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td valign='top'>";
        wcmGUI::openFieldset(_ACCOUNT);
        echo '&nbsp;</br>';
        $super_supervisors_array = array('fchasseriau', 'AFP-LEHMANN', 'AFP-TAROT', 'AFP-SALAME', 'AFP-WOOD', 'AFP-GIRAUDON', 'AFP-NAMBIAR', 'AFP-PETERS', 'AFP-MERICHE', 'AFP-CHATELIER', 'AFP-BOHN', 'csereis');
        
        if ($currentUser->isAdministrator || in_array($currentUser->login, $super_supervisors_array)) {
            $profiles = array('_ACCOUNT_PROFILE_DEMO'=>getConst(_ACCOUNT_PROFILE_DEMO), '_ACCOUNT_PROFILE_CLIENT'=>getConst(_ACCOUNT_PROFILE_CLIENT), '_ACCOUNT_PROFILE_MANAGER'=>getConst(_ACCOUNT_PROFILE_MANAGER), '_ACCOUNT_PROFILE_SUPERVISOR'=>getConst(_ACCOUNT_PROFILE_SUPERVISOR));
            wcmGUI::renderDropdownField('profile', $profiles, $bizobject->profile, getConst(_DASHBOARD_MODULE_ACCOUNT_PROFILE));
        } elseif ($currentUserAccount->isChiefManager()) {
            $profiles = array('_ACCOUNT_PROFILE_DEMO'=>getConst(_ACCOUNT_PROFILE_DEMO), '_ACCOUNT_PROFILE_CLIENT'=>getConst(_ACCOUNT_PROFILE_CLIENT), '_ACCOUNT_PROFILE_MANAGER'=>getConst(_ACCOUNT_PROFILE_MANAGER));
            wcmGUI::renderDropdownField('profile', $profiles, $bizobject->profile, getConst(_DASHBOARD_MODULE_ACCOUNT_PROFILE));
        } else {
            wcmGUI::renderHiddenField('profile', $bizobject->profile);
        }
        $year = array();
        $month = array();
        $day = array();
        
        for ($i = 2008; $i < 2018; $i++)
            $year[$i] = $i;
        for ($i = 1; $i <= 12; $i++)
            $month[$i] = $i;
        for ($i = 1; $i <= 31; $i++)
            $day[$i] = $i;
            
        echo '&nbsp;</br>';
        wcmGUI::renderDateField('expirationDate', $bizobject->expirationDate, _BIZ_EXPIRATIONDATE, 'date');
        
        if ($currentUserAccount->isChiefManager() || $currentUser->isAdministrator)
            wcmGUI::renderTextField('companyName', $bizobject->companyName, _BIZ_COMPANYNAME);
        else
            wcmGUI::renderHiddenField('companyName', $currentUserAccount->companyName);
            
        //wcmGUI::renderAutoCompletedField($url, 'companyName', $bizobject->companyName, _BIZ_COMPANYNAME, null, $acOptions);
        wcmGUI::closeFieldset();
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</form>";
        echo "</div>";
        break;
        
    case 'permissions': 
    	
    	$account = new account();
        $account->refreshByWcmUser(wcmSession::getInstance()->userId);
        
        echo "<div id=\"errorMsg\"></div>";
        /*
        echo "<link rel='stylesheet' type='text/css' href='/includes/js/treeImgs/css/tree.css' />";
        echo "<script language='JavaScript' type='text/javascript' src='/includes/js/treeImgs/Tree.js'></script>";
        
        echo "<script type='text/javascript'>
		var struct = [
			{
			'id' : 'root_1',
			'txt' : 'Root 1',
			'items' : [
			{
			'id' : 'branch_1',
			'txt' : 'Branch 1'
			},{
			'id' : 'branch_2',
			'txt' : 'Branch 2'
			}
			]
			}
			];
			
		var tree = null;

		function TafelTreeInit () {
		tree = new TafelTree('myTree', struct, {
		'generate' : true,
		'imgBase' : '/includes/js/treeImgs/imgs/',
		'width' : '100%', 
		'height' : '150px', 
		'openAtLoad' : true,
		'cookies' : false
		});
		}
		</script>";
       	echo "<div id='myTree'></div>"; 
       	*/
        /*
        echo "<script language='JavaScript' type='text/javascript' src='/includes/js/dhtmltreenode.js'></script>";

	 	echo "<div id='treeboxbox_tree2' style='width:300px; height:400px;background-color:#f5f5f5;border :1px solid Silver; overflow:auto;'></div>";
		
		echo "<script type='text/javascript'>\n";
		echo "tree2=new dhtmlXTreeObject('treeboxbox_tree2','100%','100%',0);\n";
		echo "tree2.setImagePath('/includes/js/treeImgs/');\n";
		echo "tree2.enableCheckBoxes(1);\n";
		echo "tree2.enableThreeStateCheckboxes(true);\n";
		echo "tree2.loadXML('/includes/js/treeImgs/tree3.xml');\n";
		echo "</script>\n";
        */
        echo '<div id="account">';
        
        //echo '<IFRAME src="/includes/js/test.php" width=400 height=300 scrolling=auto frameborder=0 > </IFRAME>';    
               
        echo "<ul class='toolbar'>";
        echo '<li style="float: left;"><a href="#" onclick="document.getElementById(\'selectionZone\').value=1;document.getElementById(\'list\').style.display=\'none\';document.getElementById(\'selectZone\').style.display=\'inline\'; return false;" class="replace">'._BIZ_DISPLAY_SELECTION.'</a></li>';
        echo '<li style="float: left;"><a href="#" onclick="document.getElementById(\'selectionZone\').value=2;document.getElementById(\'list\').style.display=\'inline\';document.getElementById(\'selectZone\').style.display=\'none\'; return false;" class="replace">'._BIZ_DISPLAY_RIGHTS.'</a></li>';
        echo "<li><a href='#' onclick=\"if (document.getElementById('checkCheckboxes').value == 0) {closemodal();} else {if (confirm('"._BIZ_ALERT_CLOSE."')) {closemodal();}}; return false;\" class='cancel'>"._BIZ_CLOSE."</a></li>";
        echo '<li><a href="#" onclick="addAccountPermission();document.getElementById(\'selectionZone\').value=2;document.getElementById(\'list\').style.display=\'inline\';document.getElementById(\'selectZone\').style.display=\'none\'; saveAccountPermissions('.$bizobject->id.',$(\'account_permissions_values\').value);closemodal(); return false;" class="save">'._BIZ_SAVE.'</a></li>';
        echo '<li><a href="#" onclick="addAccountPermission();document.getElementById(\'selectionZone\').value=2;document.getElementById(\'list\').style.display=\'none\';document.getElementById(\'selectZone\').style.display=\'inline\'; saveAccountPermissions('.$bizobject->id.',$(\'account_permissions_values\').value); alert(\''._BIZ_PERMISSION_APPLY.'\');loadUserIdPermissions('.$id.'); document.getElementById(\'checkCheckboxes\').value=0;return false;" class="ok">'._BIZ_APPLY.'</a></li>';
        echo "</ul>";
       	echo "<input id='selectionZone' type='hidden' value='1' name='selectionZone'/>";
        echo '<div id="content" style="height:480px;overflow:auto">';
        echo '<div class="zone">';
        echo "<form id='account_permissions' name='account_permissions'>";
        echo "<input id='checkCheckboxes' type='hidden' value='0' name='checkCheckboxes'/>";
         
        echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
        echo "<tr>";
        echo "<td valign='top'>";
        
        echo '<ul>';
        echo '<fieldset>';
        //echo '<div>';
        /*
        echo '<ul class=\'toolbar\'>';
        echo '<li><a href="#" onclick="document.getElementById(\'list\').style.display=\'inline\';document.getElementById(\'selectZone\').style.display=\'none\'; return false;" class="replace">'._BIZ_DISPLAY_RIGHTS.'</a></li>';
        echo '<li>&nbsp;</li>';
        echo '<li><a href="#" onclick="document.getElementById(\'list\').style.display=\'none\';document.getElementById(\'selectZone\').style.display=\'inline\'; return false;" class="replace">'._BIZ_DISPLAY_SELECTION.'</a></li>';
        echo '</ul>';
		*/
        echo "<input id='olduniverse' type='hidden' value='' name='olduniverse'/>";
        echo "<input id='oldservice' type='hidden' value='' name='oldservice'/>";
        
        echo '<div id="selectZone" style="display:inline; margin-left: 5px">';
        echo '<span style="text-transform:uppercase;">'._BIZ_DISPLAY_SELECTION.'</span><br /><hr>';
        //echo '<fieldset>';
        
        echo '<ul>';
        echo '<li>';
        
        if ($account->id) {
        	$siteIds = $account->getUnivers();
            $siteIds = array_unique($siteIds);        
            $currentSite = new site();
            
            $availableSites = array();
            if (empty($siteIds) && $account->isChiefManager())
            {
            	$availableSites = bizobject::getBizobjects('site', 'workflowstate="published"');
            }
            else 
            {
	            foreach ($siteIds as $id) 
	            {
	                $availableSites[] = clone $currentSite->refresh($id);
	            }
            }
        } else {
            // No defined account for current wcmUser (admin)
            $availableSites = bizobject::getBizobjects('site');
        }
        
        echo '<select name="univers" id="univers" onChange="checkBeforeChangingUniverse();">';
        echo '<option value="">'._BIZ_CHOOSE_UNIVERSE.'</option>';
        foreach ($availableSites as $site)
            echo '<option value="'.$site->id.'">'.$site->title.'</option>';
        echo '</select>';
        echo '<div id="selectService" style="display:inline; margin-left: 5px">';
        echo '<select name="services" id="services" style="width:150px;" disabled=disabled>';
        echo '<option value="">&nbsp;</option>';
        echo '</select>';
        
        //echo '<a href="#" onclick="addAccountPermission();document.getElementById(\'list\').style.display=\'inline\';document.getElementById(\'selectZone\').style.display=\'none\'; return false;" class="list-builder"><span>'._BIZ_ADD.'</span></a>';
        echo '</div>';
        echo '<div id="selectRubrique" style="display:inline; margin-left: 5px">';
        echo '</div>';
        
              
        echo '</li>';      
        echo '</ul>';
        //echo '<div id="selectRubrique" style="display:inline; margin-left: 5px">';
        //echo '</div>';
        /*
        echo '<ul>';
        echo '<li>';
        echo '<a href="#" onclick="addAccountPermission();document.getElementById(\'list\').style.display=\'inline\';document.getElementById(\'selectZone\').style.display=\'none\'; return false;" class="list-builder"><span>'._BIZ_ADD.'</span></a>';
        echo '</li>';
        echo '</ul>';
        */
        //echo '</fieldset>';
        echo '</div>';
        //echo '<br />';
        echo '<div id="list" style="display:none">';
        echo '<span style="text-transform:uppercase;">'._BIZ_DISPLAY_RIGHTS.' : </span><br /><hr>';
        //echo '<fieldset>';
        echo '<ul>';
        echo '<li>';
        echo '<ul id="permissions_list" class="tags">';
        // Init Puces From account $bizobject permissions
        $account_permissions_values = '';
        if (count($bizobject->permissions) > 0) 
        {   	
        	//print_r($bizobject->permissions);
        	require_once(WCM_DIR . '/business/api/toolbox/biz.relax.toolbox.php');
        
        	$i=1;
        	foreach ($bizobject->permissions as $univers=>$services) 
            {
            	$site = new site();
                $site->refresh($univers);
                        
            	echo "<li class='column".$i."' style='clear:both'><b>"._BIZ_LABEL_UNIVERSE." : ".$site->title."</b></li>";
                
            	foreach ($services as $service=>$rubriques) 
                {
                	if ($service == "*")
                	{
                		echo '<li class="column'.$i.'" style="margin-left:10px;clear:both" id="'.$id.'"><em>'._BIZ_ALL_SERVICES.'</em></li>';	
                	    $id = $univers.'||'.$service.'||*';                    
                        $account_permissions_values = ($account_permissions_values) ? $account_permissions_values.'##'.$id : $id;
                    	break;
                	}
                	
                	if (sizeof($rubriques)>0)
                	{
                		//echo '<li class="column'.$i.'" style="margin-left:10px;clear:both" id="'.$id.'"><em><b>'.$service.' : </b></em></li><ul>';
                		echo '<li class="column'.$i.'" style="margin-left:10px;clear:both" id="'.$id.'"><em><b>'.getServiceTrad($site->language, $service).' : </b></em></li><ul>';
                	}
                	
                	$tempArray = array();
                	foreach ($rubriques as $rubrique) 
                    {    
                    	// case all topics cochée
                    	if ($rubrique == "*")
                        	echo '<li style="margin-left:30px;clear:both"><em><i>'._BIZ_ALL_CHANNELS.'</i></em></li>';
                        else
                        {
                        	// on range les résultats dans un tableau par pilier avec le parentId                	
	                    	$channel = new channel();
	                    	$channel->refresh($rubrique);
	                    	if (!empty($channel->parentId))
	                    		$tempArray[$channel->parentId][] = $channel->title;
                        }
                        
                        // on met à jour la variable cachée des permissions                		                    	
                        $id = $univers.'||'.$service.'||'.$rubrique;                    
                        $account_permissions_values = ($account_permissions_values) ? $account_permissions_values.'##'.$id : $id;
                    }
                	
                    // si des catégories sont présentes , on les affiche            		                    	                     
                    if (!empty($tempArray))
                    {
                    	ksort($tempArray);                  	
                    	$channelTemp = new channel();
	                    foreach ($tempArray as $key => $value)
                    	{
                    		// on regroupe par pilier            		                    	                     
                    		$channelTemp->refresh($key); 
	                        echo '<li style="margin-left:30px;clear:both"><em><i>'.$channelTemp->title.'</i> : </em>';   		                            	
                    		
	                        if (!empty($value))
                    		{
                    			foreach($value as $val)	
                    				echo '<em>'.$val.'</em>'; 
                    		}
                    		echo "</li>";
                    	}
                    }
                                      	
                    if (sizeof($rubriques)>0)
                		echo '</ul>';                  
                }
                $i++;
            }
        }
        echo '</ul>';
        echo '</li>';
        echo '</ul>';
        //echo '</fieldset>';
        echo '</div>';
        
        //echo '</div>';
        echo '</fieldset>';
        //echo "########".$account_permissions_values;
        wcmGUI::renderHiddenField('account_permissions_values', $account_permissions_values);
        echo "</ul>";
        
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "<br/><br/>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        
        break;
        
    case 'bulkPermissions':
        echo "<div id=\"errorMsg\"></div>";
        echo '<div id="account">';
        echo "<table style='border: 0px none; ' width='98%'>";
        echo "<tr>";
        echo "<td width='20%' valign='top'><div id=\"accountList\" style=\"overflow: auto; height: 400px;\">";
        $accountList = json_decode($targetid);
        $account = new account();
        foreach ($accountList as $anAccount) {
            $accountId = substr($anAccount, strrpos($anAccount, '_') + 1);
            $account->refresh($accountId);
            echo '<div id="account_"'.$account->id.'>'.str_replace('|', ' ', $account->wcmUser_name).'</div>';
        }
        echo "</div></td>";
        echo "<td valign='top'>";
        echo '<div id="content">';
        echo '<div class="zone">';
        echo '<div class="action-header"><span  style="margin-left:5px; font-weight:bold">Charger les permissions de</span>';
        $url = $config['wcm.backOffice.url'].'business/ajax/autocomplete/wcm.account.php';
        $acOptions = array('url'=>$url, 'paramName'=>'prefix', 'parameters'=>'');
        wcmGUI::renderAutoCompletedField($url, 'fromAccount', null, null, array('style'=>'width:200px; margin-left:170px'), $acOptions, true);
        echo '<ul><li><a href="#" onclick="loadUserPermissions($(\'fromAccount\').value); return false;" class="chapter" style="float:right">'._OK.'</a></li></ul>';
        echo '</div>';
        wcmGUI::openFieldset('Permissions');
        
        echo '<ul>';
        echo '<li>';
        
        $account = new account();
        $account->refreshByWcmUser(wcmSession::getInstance()->userId);
        if ($account->id) {
            $siteIds = $account->getUnivers();
            $siteIds = array_unique($siteIds);
            $currentSite = new site();
            $availableSites = array();
            foreach ($siteIds as $id) {
                $availableSites[] = $currentSite->refresh($id);
            }
        } else {
            // No defined account for current wcmUser (admin)
            $availableSites = bizobject::getBizobjects('site');
        }
        
        echo '<select name="univers" id="univers" onChange="javascript:populeServices();">';
        echo '<option value=""></option>';
        foreach ($availableSites as $site)
            echo '<option value="'.$site->id.'">'.$site->title.'</option>';
        echo '</select>';
        echo '<div id="selectService" style="display:inline; margin-left: 5px">';
        echo '<select name="services" id="services" style="width:150px;" disabled=disabled>';
        echo '<option value="">&nbsp;</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div id="selectRubrique" style="display:inline; margin-left: 5px">';
        wcmGUI::renderSingleTag('input', array('id'=>'rubrique', 'disabled'=>'disabled', 'style'=>'float: none; width:150px; margin-left: 5px', 'type'=>'text'));
        echo '</div>';
        
        echo '<a href="#" onclick="addAccountPermission(); return false;" class="list-builder"><span>'._BIZ_ADD.'</span></a>';
        
        echo '</li>';
        echo '<li>';
        echo '<ul id="permissions_list" class="tags">';
        $account_permissions_values = '';
        echo '</ul>';
        echo '</li>';
        echo '</ul>';
        
?>
<input id="overWrite" type="hidden" value="0" name="overWrite"/>
<table border='0'>
    <tr>
        <td>
            <input id="_wcmBoxoverWrite" type="checkbox" name="_wcmBoxoverWrite" onclick="$('overWrite').value=(this.checked)?'1':'0';"/>
        </td>
        <td>
            OverWrite ?
        </td>
    </tr>
</table>
<?php 
wcmGUI::closeFieldset();
wcmGUI::renderHiddenField('account_permissions_values', $account_permissions_values);
wcmGUI::renderHiddenField('targetid', $targetid);
echo "<ul class='toolbar'>";
echo '<li><a href="#" onclick="saveBulkPermissions($(\'targetid\').value,$(\'account_permissions_values\').value,$(\'overWrite\').value);closemodal(); return false;" class="save">'._BIZ_SAVE.'</a></li>';
echo "<li><a href='#' onclick=\"closemodal(); return false;\" class='cancel'>"._BIZ_CANCEL."</a></li>";
echo "</ul>";
echo "</div>";
echo "</div>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</div>";
break;

case 'bulkDelete':
    echo "<div id=\"errorMsg\"></div>";
    echo '<div id="account">';
    echo "<table style='border: 0px none; ' width='98%'>";
    echo "<tr>";
    echo "<td width='20%' valign='top'><div id=\"accountList\" style=\"overflow: auto; height: 400px;\">";
    $accountList = json_decode($targetid);
    $account = new account();
    foreach ($accountList as $anAccount) {
        $accountId = substr($anAccount, strrpos($anAccount, '_') + 1);
        $account->refresh($accountId);
        echo '<div id="account_"'.$account->id.'>'.str_replace('|', ' ', $account->wcmUser_name).'</div>';
    }
    echo "</div></td>";
    echo "<td valign='top'>";
    echo '<div id="content">';
    echo '<div class="zone">';
    echo "SUPPRESSION A IMPLEMENTER<br/><br/><br/><br/><br/><br/>";
    echo "<ul class='toolbar'>";
    echo "<li><a href='#' onclick=\"closemodal(); return false;\" class='cancel'>"._BIZ_CANCEL."</a></li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
    break;
}


?>
