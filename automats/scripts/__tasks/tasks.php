<?php 
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
$config = wcmConfig::getInstance();

$relaxTasksIds = relaxTask::getLaunchingTasks();
if (count($relaxTasksIds)) {

    foreach ($relaxTasksIds as $relaxTaskId) {
		
        exec ($config['php.exe'].' -q -c '.$config['php.cliini'].' -f '.dirname(__FILE__).'/taskLauncher.php id='.$relaxTaskId.' > '.dirname(__FILE__).'/LOG &');

    }
}
//on lance la vérification de task crashé une fois toutes les heures
//sinon quand une tache plante, une alerte est envoyé toutes les minutes ... c'est lourd à gerer en pleine nuit !
if(date("i") == "00"){
	$relaxTasksIds = relaxTask::getCrashingTasks();
}

