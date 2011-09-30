<?php
/**
 * Project:     WCM
 * File:        business/tasks/import.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * 
 * This task generate a rule (generationSet, generation or generationContent)
 *
 * When a task is execute the WCM api is automatically loaded
 * and the following global variables are available
 *  $session    The current wcmSession instance
 *  $project    The current wcmProject instance
 *  $config     The current wcmConfig instance
 *  $logger     The default logger used to trace message and error
 *  $parameters An assoc array of extra parameters
 *
 *
 * IMPORTANT: all output should be done through the $logger and fatal error
 * should be raised using the trigger_error() method
 */
    
    // retrieve class of plug-in to instanciate
    $plugin = getArrayParameter($parameters, 'plugin');
    if ($plugin == null)
    {
        $logger->logError(_MISSING_PLUGIN_PARAMETER);
        exit;
    }

    // instanciate import plug-in
    if (!class_exists($plugin))
    {
        $logger->logError(_INVALID_PLUGIN_PARAMETER . $plugin);
        exit;
    }    

    // compute start time
    $startTime = microtime(true);
    $logger->logMessage(_IMPORT_STARTING);
    
    $parameters['logger'] = $logger;

    // execute import
    $import = new $plugin($parameters);
    $import->process();
    
    // display duration
    $duration = microtime(true) - $startTime;
    $logger->logMessage(sprintf(_IMPORT_COMPLETED_IN_X_SECONDS, $duration));
