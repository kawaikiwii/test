<?php 
// Retrieve parameters from CLI
$parameters = array();
foreach ($argv as $argument) {
    $parts = explode('=', $argument, 2);
    if (count($parts) == 2) {
        $arg = trim($parts[0], ' "');
        $val = trim($parts[1], ' "');
        $parameters[$arg] = $val;
    }
}

// Init WCM api
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

// Launch Task

if (isset($parameters['id'])) {
    $relaxTask = new relaxTask();
    $relaxTask->refresh($parameters['id']);
    $relaxTask->launch();
}
