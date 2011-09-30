<?php

/**
 * Project:     WCM
 * File:        task_logs.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */
require_once dirname(__FILE__).'/../../../initWebApp.php';

$config = wcmConfig::getInstance();

$id     = getArrayParameter($_REQUEST, "id", 0);

$task = new relaxTask();
$task->refresh($id);

echo '<div id="tasks">';
	echo "<table style='border: 0px none; ' width='98%'>";
	echo "<tr>";
		echo "<td valign='top'>";
			echo '<div id="taskList" name="taskList" style="overflow: auto; width:180px; height: 400px;margin-right:5px; ">';
			$path = $task->getlogPath();
			//$path = WCM_DIR . '/logs/traces/2009-12/';
			if (is_dir($path))
			{
				$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
				$files = array();
				foreach ($dir as $d)
					$files[$d->getFilename()] = $path.$d->getFilename();

				krsort($files);
				$i = 0;
				foreach ($files as $filename => $path)
				{
					echo '<li style="cursor:pointer;margin:2px; padding:2px" onClick="getFileContent(\''.$path.'\');">'.$filename.'</li>';
					$i++;
				}
			}
			echo "</div>";
		echo "</td>";
		echo "<td valign='top'>";
			echo '<div class="log">';
				echo '<div id="fileContent" style="overflow: auto;height: 400px; width:600px; border: solid 1px grey" >';
				echo "</div>";
			echo "</div>";
		echo "</td>";
	echo "</tr>";
	echo "</table>";
echo "</div><br />";
unset ($task);
?>
<script language='javascript'>
getFileContent = function (filename)
{
	$("fileContent").innerHTML = "<div class='wait' style='display:inline;'>Loading...</div>";
	wcmBizAjaxController.call("biz.loadTasksLogs", {
		filename: filename,
		divId: 'fileContent'
    });
}
</script>
