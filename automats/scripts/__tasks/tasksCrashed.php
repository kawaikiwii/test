<?php 
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
$config = wcmConfig::getInstance();

$relaxTasksIds = relaxTask::getCrashingTasks();
if (count($relaxTasksIds)) {
	echo "Tâche(s) crashée(s)";
}else{
	echo "Aucune tâche crashée";
}