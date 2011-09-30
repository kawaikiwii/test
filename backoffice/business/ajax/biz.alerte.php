<?php

/**
 * Project:     WCM
 * File:        biz.alerte.php
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
$command	= getArrayParameter($_REQUEST, "command", null);
$loginAs	= getArrayParameter($_REQUEST, "loginAs", null);
$alerteId	= getArrayParameter($_REQUEST, "alerteId", null);
$formDatas	= getArrayParameter($_REQUEST, "formDatas", null);
$perimeter	= getArrayParameter($_REQUEST, "perimeter", null);

$account = new account();
$account->refresh($loginAs);

$relaxTask = new relaxTask();
if ($alerteId)
{
	$relaxTask->refresh($alerteId);
	$relaxTask->serializedForm = unserialize($relaxTask->serializedForm);
}

$formsDatasArray = array();
if ($formDatas)
{
	$temp = explode('&',$formDatas);
	foreach ($temp as $item)
	{
		$temp2 = explode('=',$item);
		$formsDatasArray[urldecode($temp2[0])] = urldecode($temp2[1]);
	}
}

switch ($command)
{
	case 'refresh':
		break;

	case 'delete':
		$relaxTask->delete();
		$relaxTask = new relaxTask();
		break;

	case 'add':
		$relaxTask->bind($formsDatasArray);
		$relaxTask->serializedForm = $formsDatasArray;
		
		$exportRule = new exportRule();
		
		// init default export Rule used for mailing
		$exportRule->refreshByCode('pushmail','pushmail');
		if ($exportRule->id)
			$relaxTask->exportRulesIds = $exportRule->id;

		// max 10 objects in alert pushmail 
		$relaxTask->limit = 10;
				
		// save perimeter array in relaxTask perimeter property
		if (isset($perimeter) && !empty($perimeter))
		{
			$tabPerimeter = array();
			// get initial perimeter structure returned by treenode
			$tempTab = explode(",", $perimeter);
			foreach ($tempTab as $values)
				$tabPerimeter[] = $values;
			
			//convert perimeter structure in a compatible array structure used for permissions in xmlTree init
			$perm = array();
			foreach ($tabPerimeter as $value)
    		{
    			$temp = explode("_", $value);
    			if (sizeof($temp)>2)
    				$perm[$temp[0]][$temp[1]][] = $temp[2];
    		}
    		
			$relaxTask->perimeter = serialize($perm);
			//add save because makeQueryFromSerializedForm need perimeter information before general save
			$relaxTask->save();	
		}	
		
		$relaxTask->makeQueryFromSerializedForm();
		
		$relaxTask->save();
		
		$relaxTask->setGroupPermission(1,wcmPermission::P_DELETE);
		// si la tâche n'a jamais été exécutée, on l'initialise
		//if (empty($relaxTask->lastExecutionDate) && !empty($relaxTask->id))
		//On initialise dans tous les cas pour éviter une plage de dates trop étendue entre la création et l'activation
			relaxTask::initialize($relaxTask->id);
				
		//$relaxTask = new relaxTask();
		break;
}

// Response
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<ajax-response>\n";
echo "<response type=\"item\" id=\"alerte\"><![CDATA[";

	echo "<center><div id='alertSaveMsg' style='visibility:hidden;width:400px; height:20px; background:#F282F6;font-size: 12px;font-weight: bold; border:1px solid #333;padding:5px'></div></center>";
	echo "<table style='border: 0px none; ' width='99%'>";
	echo "<tr>";
	echo "<td width='20%' valign='top'><div id=\"alerteList\" name=\"alerteList\" style=\"overflow: auto; height: 400px;\">";
	echo "<fieldset style='padding: 0.2em 0.5em;border:1px solid blue;'>";
	echo "<legend>"._PM_CURRENT_ALERTS."</legend><br />";
	echo "<ul class='toolbar newtask'>";
	echo '<li><a href="#" onclick="manageAlerte(\'refresh\','.$loginAs.',0,\'\'); putAlertMessage(\''._PM_NEW_FORM.'\'); return false;" class="cancel">'._PM_NEW.'</a></li>';
	echo "</ul>";
	$alerteList = bizobject::getBizobjects('relaxTask','loginAs = '.$loginAs.' AND type="alerte"');
	if (!empty($alerteList))
	{
		foreach ($alerteList as $anAlerte)
		{
			// display selected alert
			echo '<div id="alerte_'.$anAlerte->id.'" style="cursor:pointer;color:blue" onclick="manageAlerte(\'load\','.$loginAs.','.$anAlerte->id.',\'\');">';
			if($anAlerte->enable)
				echo '<img border="0" align="middle" src="/img/checked.gif" title="'._BIZ_ACTIVE.'">&nbsp;';
			else
				echo '<img border="0" align="middle" src="/img/remove.gif" title="'._BIZ_INACTIVE.'">&nbsp;';
			if (($anAlerte->id == $alerteId) && !empty($relaxTask->id))
				echo '<a href="#" style="color:red;font-weight:bold">';
			else
				echo '<a href="#">';
			echo $anAlerte->name.'</a></div>';
		}
	}
	else
		echo '<div align="center">-- '._PM_NONE.' --</div>';
			
	echo "</fieldset>";
	echo "</div></td>";
	echo "<td valign='top'>";
		echo '<div id="content" style="overflow: auto; height: 550px;">';
			echo '<div class="zone">';
				echo '<form name="alerteForm" id="alerteForm">';
				echo '<fieldset style="margin-left:5px">';
				echo "<legend>"._PM_TASK."</legend>";
				echo "<ul>";
					wcmGUI::renderTextField('name', $relaxTask->name, _BIZ_NAME, array('style' => 'float:none;'));
					wcmGUI::renderDropdownField('planning', relaxTask::getDefinedPlannings(), $relaxTask->planning, _PM_FREQUENCY, array('style' => 'float:none;'));
					wcmGUI::renderHiddenField('id', $relaxTask->id);
					wcmGUI::renderHiddenField('type', 'alerte');
					wcmGUI::renderHiddenField('companyName', ($relaxTask->companyName) ? $relaxTask->companyName : $account->companyName);
					wcmGUI::renderHiddenField('loginAs', $loginAs);
					
					if(!empty($relaxTask->id))
						relaxGUI::renderRelaxBooleanField('enable', $relaxTask->enable, _BIZ_ACTIVE);
					else
						relaxGUI::renderRelaxBooleanField('enable', 1, _BIZ_ACTIVE);
					echo "<li>";
					echo "<label style=''>".textH8(_PM_CATEGORIES)."</label>";
					$pm_cat = "P";
					if(strpos($relaxTask->query,"channelIds:"))
						$pm_cat = "P+S";
					echo "<input type='radio' name='pm_cat' id='pm_cat1' value='P' style='width:30px;float:none;position:relative;left:-8px;'";
					if($pm_cat == "P")
						echo " checked";
					echo "><span style='position:relative;left:-8px;'>"._PM_CATEGORIES_P."</span>";
					echo "<input type='radio' name='pm_cat' id='pm_cat2' value='P+S' style='width:30px;float:none;position:relative;left:-8px;'";
					if($pm_cat == "P+S")
						echo " checked";
					echo "><span style='position:relative;left:-8px;'>"._PM_CATEGORIES_PS."</span>";
					echo "</li>";
				echo "</ul>";
				echo "</fieldset>";
				
				echo '<fieldset style="margin-left:5px">';
				echo "<legend>"._PM_QUERY."</legend>";
				echo "<ul>";
				echo '<li>'._PM_CONTAINING.'&nbsp;<img src="'.$config['wcm.backOffice.url'].'/img/icons/help.gif" style="cursor:pointer" onclick="if ($(\'queryContaining\').style.display==\'none\'){$(\'queryContaining\').appear();}else{$(\'queryContaining\').fade();}" /><br />';
					echo "<div id='queryContaining' style='display:none;width:90%;margin-top:5px;background:#A9D0F5;font-size:12px;font-weight:bold;border:1px solid #333;padding:5px'>"._PM_EXAMPLE_CONTAINING."</div>";
					wcmGUI::renderTextField('content', (isset($relaxTask->serializedForm['content'])) ? $relaxTask->serializedForm['content'] : '', '', array('style' => 'float:none;width:360px'));
				echo '</li>';
				echo '<li>'._PM_EXCLUDING.'&nbsp;<img src="'.$config['wcm.backOffice.url'].'/img/icons/help.gif" style="cursor:pointer" onclick="if ($(\'queryExcluding\').style.display==\'none\'){$(\'queryExcluding\').appear();}else{$(\'queryExcluding\').fade();}" /><br />';
					echo "<div id='queryExcluding' style='display:none;width:90%;margin-top:5px;background:#A9D0F5;font-size:12px;font-weight:bold;border:1px solid #333;padding:5px'>"._PM_EXAMPLE_EXCLUDING."</div>";
					wcmGUI::renderTextField('exclude', (isset($relaxTask->serializedForm['exclude'])) ? $relaxTask->serializedForm['exclude'] : '', '', array('style' => 'float:none;width:360px'));
				echo '</li>';
				
				// show query if exist
				if (!empty($relaxTask->query))
				{
					/*// init lastExecutionDate if not defined
					if (empty($relaxTask->lastExecutionDate))
        				$lastExec = str_replace(' ', 'T', date("Y-m-d H:i:s"));
					else 
						$lastExec = str_replace(' ', 'T', $relaxTask->lastExecutionDate);
					
					// init nextExecutionDate if not defined
					if (empty($relaxTask->nextExecutionDate))
        				$nextExec = str_replace(' ', 'T', $relaxTask->nextIterationDate());
					else
						$nextExec = str_replace(' ', 'T', $relaxTask->nextExecutionDate);
					
					$req = str_replace('__LAST__', $lastExec, $relaxTask->query);
					$req = str_replace('__NEXT__', $nextExec, $req);
						
					echo '<li><img src=\'/img/actions/parameters.gif\' border=0>&nbsp;<a href="#" onclick="if ($(\'queryZone\').style.display==\'none\'){$(\'queryZone\').appear();}else{$(\'queryZone\').fade();}">'._PM_FINAL_QUERY.'</a><br />';
					echo "<div id='queryZone' style='display:none;width:90%; margin-top:5px;background:#A9D0F5;font-size: 12px;font-weight: bold; border:1px solid #333;padding:5px'>".$req."</div>";
					echo '</li>';*/
					
					echo '<li><img src=\'/img/icons/page_tick.gif\' border=0>&nbsp;<a href="#" onclick="if (confirm(\''._PM_PREVIEW_TASK.'\')) {if (document.getElementById(\'treenodes\').contentWindow.tree2.getAllChecked() == \'\') {alert(\''._PM_MISSING_CATEGORY.'\');} else {if(manageAlerte(\'add\','.$loginAs.',$(\'id\').value,\'\', document.getElementById(\'treenodes\').contentWindow.tree2.getAllChecked()) == \'add\') previewTask($(\'id\').value,'.$loginAs.');return false;}}">'._PM_TEST_TASK.'</a><br /></li>';
				}
				
				echo "</ul>";
				echo "</fieldset>";
				echo "<div><ul class='toolbar'>";
				echo '<li><a href="#" onclick="if(document.getElementById(\'id\').value != 0 && confirm(\''._PM_CONFIRM_DELETE.'\')){manageAlerte(\'delete\','.$loginAs.',$(\'id\').value,\'\'); putAlertMessage(\''._PM_DELETED_ALERT.'\'); return false;}" class="delete">'._DELETE.'</a>&nbsp;</li>';
				echo '<li><a href="#" onclick="if(document.getElementById(\'name\').value == \'\'){alert(\''._PM_MISSING_NAME.'\');} else if (document.getElementById(\'treenodes\').contentWindow.tree2.getAllChecked() == \'\') {alert(\''._PM_MISSING_CATEGORY.'\');} else {if(manageAlerte(\'add\','.$loginAs.',$(\'id\').value,\'\', document.getElementById(\'treenodes\').contentWindow.tree2.getAllChecked()) == \'add\') putAlertMessage(\''._PM_SAVED_ALERT.'\'); return false;}" class="save">'._BIZ_SAVE.'</a>&nbsp;</li>';
				echo "</ul></div>";
				echo "</form>";
			echo "</div>";
		echo "</div>";
	echo "</td>";
	echo "<td width='370' valign='top'>";
	// if relaxtask exist init perimeter
	if (isset($relaxTask->id))
		echo '<IFRAME name="treenodes" id="treenodes" src="/business/modules/ugc/account/permissionsAlert.php?id='.$loginAs.'&taskId='.$relaxTask->id.'" width=355 height=550 scrolling=auto frameborder=0 > </IFRAME>';
	else
		echo '<IFRAME name="treenodes" id="treenodes" src="/business/modules/ugc/account/permissionsAlert.php?id='.$loginAs.'" width=355 height=550 scrolling=auto frameborder=0 > </IFRAME>';
	echo "</td>";
	echo "</tr>";
	echo "</table>";
echo "]]></response>";
echo "</ajax-response>";
