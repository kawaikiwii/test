<?php

/**
 * Project:     WCM
 * File:        biz.relaxTask.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve parameters
$command    = getArrayParameter($_REQUEST, "command", null);
$divId	    = getArrayParameter($_REQUEST, "divId", 0);
$taskId	    = getArrayParameter($_REQUEST, "taskId", 0);
$datas	    = getArrayParameter($_REQUEST, "datas", null);
//$type       = getArrayParameter($_REQUEST, "displayFilter", "standard");
$type       = getArrayParameter($_REQUEST, "displayFilter", "showactive");
$sortOrder  = getArrayParameter($_REQUEST, "sortOrder", "ASC");
//$sortField  = getArrayParameter($_REQUEST, "sortField", "nextExecutionDate");
$sortField  = getArrayParameter($_REQUEST, "sortField", "name");
$searchdata	= getArrayParameter($_REQUEST, "searchdata", '');

//print_r($_REQUEST);

$formsDatas = array();
if ($datas)
{
	$temp = explode('&',$datas);
	foreach ($temp as $item)
	{
		$temp2 = explode('=',$item);
		$formsDatas[urldecode($temp2[0])] = urldecode($temp2[1]);
	}
}

$msg = '';

switch ($command)
{
	case 'refresh':
		break;

	case 'enable':
		relaxTask::enable($taskId);
		break;

	case 'disable':
		relaxTask::disable($taskId);
		break;

	case 'stop':
		relaxTask::stop($taskId);
		break;

	case 'express':
		relaxTask::expressLaunch($taskId);
		break;

	case 'init':
		relaxTask::initialize($taskId);
		break;

	case 'delete':
		$relaxTask = new relaxTask();
		$relaxTask->refresh($taskId);
		$relaxTask->delete();
		break;

	case 'insert':
	case 'update':
		$relaxTask = new relaxTask();
		$relaxTask->refresh($taskId);
		$relaxTask->bind($formsDatas);
		$relaxTask->save();
		break;
}


// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

date_default_timezone_set('Europe/Paris');
// Xml output
header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$date = date('Y-m-d H:i:s');       
$hour = strtotime($date.' - 1 hour');
// Retrieve relaxTasks
$where = '';
if ($type == 'showactive')
	$where = "enable=1";
else if ($type == 'showinactive')
	$where = "(enable!=1 OR enable IS NULL)";
else if ($type == 'showrunning')
	$where = "enable=1 AND nextExecutionDate <= '".date('Y-m-d H:i:s',$hour)."' AND (processId<>0 AND processId IS NOT NULL)";
else if ($type != 'all')
	$where = "type='".$type."'";
else if ($type == 'all')
	$where = "type IS NOT NULL";
	
if (!empty($searchdata))
	$where .= " AND (name LIKE '%".$searchdata."%' OR companyName LIKE '%".$searchdata."%')";	
	
$relaxTasks = bizobject::getBizobjects('relaxTask', $where, $sortField.' '.$sortOrder);

// Write ajax response
echo "<ajax-response>\n";
echo "<response type='item' id='".$divId."'><![CDATA[";
if ($msg != '')
	echo "<div style=\"background-color:red; text-align: center; font-weight:bolder;\">".$msg."</div>";
else
{	
		echo "<form name='myform' id='myform' style=''>";
		echo "<div class='tabular-presentation' style='margin-top:2px'>";
		echo "<input type='hidden' id='displayFilter' name='displayFilter' value='".$type."' />";
		echo "<input type='hidden' id='sortOrder' name='sortOrder' value='".$sortOrder."' />";
		echo "<div id='refreshButton' name='refreshButton'>";
			echo "<table border=0 cellpadding=0 cellspacing=0><tr><td valign=center><img src='img/refresh.gif' style='cursor:pointer' onClick=\"ajaxRelaxTask('refresh', '".$divId."', null, null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\"/></td><td valign=center align=center style='width:40px;font-size:8.5px;'>[<span id='counter' name='counter' style='width:30px; text-align:right;'>0</span>s.]</td>";
					echo "<td valign=center align=center>";
						echo "<div class='tabular-presentation' style='margin-top:2px'><ul class='filters' style='height:20px'>";
							echo "  <li><a href=\"javascript:divWait(); ajaxRelaxTask('refresh', 'results', null, null, 'standard', '".$sortField."', '".$sortOrder."', '".$searchdata."');\" ";
								if ($type == 'standard')
									echo "class='selected'";
								echo ">Standard</a></li>";
							echo "  <li><a href=\"javascript:divWait(); ajaxRelaxTask('refresh', 'results', null, null, 'alerte', '".$sortField."', '".$sortOrder."', '".$searchdata."');\" ";
								if ($type == 'alerte')
									echo "class='selected'";
								echo ">Alertes</a></li>";
							$sysobject = new wcmUser($project);
							$sysobject->refresh(wcmSession::getInstance()->userId);
							if ($sysobject->isAdministrator)
							{
							echo "  <li><a href=\"javascript:divWait(); ajaxRelaxTask('refresh', 'results', null, null, 'all', '".$sortField."', '".$sortOrder."', '".$searchdata."');\" ";
								if ($type == 'all')
									echo "class='selected'";
								echo ">"._ALL."</a></li>";
							}
						
						echo "  <li><a href=\"javascript:divWait(); ajaxRelaxTask('refresh', 'results', null, null, 'showactive', '".$sortField."', '".$sortOrder."', '".$searchdata."');\" ";
						if ($type == 'showactive')
							echo "class='selected'";
						echo ">Active</a></li>";
							
						echo "  <li><a href=\"javascript:divWait(); ajaxRelaxTask('refresh', 'results', null, null, 'showinactive', '".$sortField."', '".$sortOrder."', '".$searchdata."');\" ";
						if ($type == 'showinactive')
							echo "class='selected'";
						echo ">Inactive</a></li>";
						
						echo "  <li><a href=\"javascript:divWait(); ajaxRelaxTask('refresh', 'results', null, null, 'showrunning', '".$sortField."', '".$sortOrder."', '".$searchdata."');\" ";
						if ($type == 'showrunning')
							echo "class='selected'";
						echo ">Running</a></li>";

						echo " <li><input type='text' id='searchdata' value='".$searchdata."'>";
						echo "<input type='submit' value='"._SEARCH."' onClick=\"javascript:divWait(); ajaxRelaxTask('refresh', 'results', null, null, '".$type."', '".$sortField."', '".$sortOrder."', document.getElementById('searchdata').value);\"></li>";
	
						echo "</ul></div>";
					echo "</td>";
					echo "<td style='text-align:center;font-weight:bold;'>";
						echo "<div id='relaxTaskSummary' name='relaxTaskSummary' style='width:200px;'>".count($relaxTasks)." Tache(s) - ".date("H:i:s")."</div>";
					echo "</td>";
					
					echo "<td valign=center align=center>";
					echo "<div class='tabular-presentation' style='margin-top:2px'><ul class='filters' style='height:20px'>";
						
					echo "  <li><a href=\"javascript:toggleCheckBoxes('myform');\">tout cocher/décocher</a></li>";
					echo "  <li><a href=\"javascript:checkBoxesAction('myform', 'enable', '".$divId."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\">Activer</a></li>";
					echo "  <li><a href=\"javascript:checkBoxesAction('myform', 'disable', '".$divId."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\">Desactiver</a></li>";
					//echo "  <li><a href=\"javascript:checkBoxesAction('myform', 'stop', '".$divId."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\">Stop</a></li>";
					echo "  <li><a href=\"javascript:checkBoxesAction('myform', 'init', '".$divId."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\">Init</a></li>";
					echo "  <li><a href=\"javascript:checkBoxesAction('myform', 'express', '".$divId."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\">Exécuter</a></li>";
						
					echo "</ul></div>";
					echo "</td>";
					echo "</tr>";
			echo "</table>";
		echo "</div>";
		echo "<div class='wait' style='display:none'>Loading...</div>";
	echo "<br />";
	echo "<table id='relaxTasks'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th width='30'>&nbsp;</th>";
	echo '<th><img src="img/action-sort.gif" />&nbsp;<a href="#" onClick="toggleSortOrder(\'name\' ,\'ASC\');">Nom</a></th>';
	echo '<th><img src="img/action-sort.gif" />&nbsp;<a href="#" onClick="toggleSortOrder(\'companyName\' ,\'ASC\');">Entreprise</a></th>';
	echo "<th>Type</th>";
	$timezone = ini_get('date.timezone');
	echo '<th><img src="img/action-sort.gif" />&nbsp;<a href="#" onClick="toggleSortOrder(\'lastExecutionDate\',\'ASC\');">Derniere execution ('.$timezone.')</a></th>';
	echo '<th><img src="img/action-sort.gif" />&nbsp;<a href="#" onClick="toggleSortOrder(\'nextExecutionDate\',\'ASC\');">Prochaine execution ('.$timezone.')</a></th>';
	echo '<th>Planning</th>';
	echo '<th><img src="img/action-sort.gif" />&nbsp;<a href="#" onClick="toggleSortOrder(\'enable\',\'ASC\');">Active</a></th>';
	echo "<th width='30'>Actions</th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	
	if (count($relaxTasks)>0)
	{
		$i=0;
		foreach ($relaxTasks as $current_relaxTask)
		{
			if ($i%2==0)
				echo "<tr id='relaxTask_".$current_relaxTask->id."' class=\"off\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off'\">";
			else
				echo "<tr id='relaxTask_".$current_relaxTask->id."' class=\"off2\" onmouseover=\"this.className='on'\" onmouseout=\"this.className='off2'\">";

			echo "<td class='actions'>";
			//echo "<ul class='three-buttons' style='margin-left:8px'>";
			echo "<ul class='four-buttons' style='margin-left:8px'>";
				echo "<li><span style='float:left;'><input type='checkbox' name='check_box_'".$current_relaxTask->id."' id='".$current_relaxTask->id."'></span></li>";
				echo "<li><a class='edit' title='"._EDIT."' href=\"javascript:openmodal('Tache','850'); modalPopup('task','update', '".$current_relaxTask->id."', '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\"><span>"._EDIT."</span></a></li>";
				echo "<li><a class='log' title='Logs' href=\"javascript:openmodal('Task Logs','800');modalPopup('task_logs','refresh', '".$current_relaxTask->id."');\"><span>LOGS</span></a></li>";
				echo "<li><a class='delete' title='"._DELETE."' href=\"javascript: if (confirm('Voulez-vous vraiment supprimer cette tache ?')) (ajaxRelaxTask('delete', '".$divId."', '".$current_relaxTask->id."',null, '".$type."', '".$sortField."', '".$sortOrder."'));\" id=''><span>"._DELETE."</span></a></li>";
			echo "</ul>";
			echo "</td>";

			echo "<td align='left'>";
				echo $current_relaxTask->name;
			echo "</td>";

			echo "<td align='center'>";
				echo $current_relaxTask->companyName;
			echo "</td>";

			echo "<td align='center'>";
				echo $current_relaxTask->type;
			echo "</td>";

			echo "<td align='center'>";
				echo $current_relaxTask->lastExecutionDate;
			echo "</td>";

			echo "<td align='center'>";
				echo $current_relaxTask->nextExecutionDate;
			echo "</td>";
			
			echo "<td align='center'>";
				echo $current_relaxTask->planning;
			echo "</td>";
			
			echo "<td align='center'>";
				if ($current_relaxTask->enable)
					echo "<img src=img/checked.gif title='Desactiver' alt='Desactiver' style='cursor:pointer' onClick=\"ajaxRelaxTask('disable', '".$divId."', '".$current_relaxTask->id."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\">";
				else
					echo "<img src=img/remove.gif title='Activer' alt='Activer' style='cursor:pointer' onClick=\"ajaxRelaxTask('enable', '".$divId."', '".$current_relaxTask->id."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\">";
			echo "</td>";

			echo "<td class='actions'>";
				if ($current_relaxTask->processId)
					echo "<img src=img/stop.gif title='Stop' alt='Stop' style='cursor:pointer' onClick=\"ajaxRelaxTask('stop', '".$divId."', '".$current_relaxTask->id."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\">";
				else
				{
					echo "<ul class='two-buttons' style='margin-left:8px'>";
						echo "<li><a class='init' title='Initialize' href=\"javascript:ajaxRelaxTask('init', '".$divId."', '".$current_relaxTask->id."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\"><span>Initialize</span></a></li>";
						echo "<li><a class='express' title='Express Launch' href=\"javascript:ajaxRelaxTask('express', '".$divId."', '".$current_relaxTask->id."',null, '".$type."', '".$sortField."', '".$sortOrder."', '".$searchdata."');\"><span>Express Launch</span></a></li>";
					echo "</ul>";
				}
			echo "</td>";

			echo "</tr>";
			$i++;
		}
	}
	else
	{
		echo "<tr><td colspan='8'> - ("._EMPTY.") - </td></tr>";
	}
	echo "</tbody>";
	echo "</table>";	
}
echo "</div>";
echo "</form>";
		
echo "]]></response>\n";
echo "</ajax-response>";

