<?php
/**
 * Project:     WCM
 * File:        api/task/wcm.taskBootstrap.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * Bootstrap used to launch a specific task's process
 *
 * This file expect parameters in the command line interface
 * _wcmProcess={process}     // process to execute
 * _wcmUserId={userId}       // user Id used in local wcmSession
 * _wcmLogPath={path}        // path where to write the process id
 * _wcmLanguage={language}   // optional language (default is from config)
 * _wcmSiteId={siteId}       // optional site id (default is from config)
 * _wcmParams={serialized..} // optional serialized parameters
 *
 */

/**
 * Custom error handler
 * -> Will redirect all error to the stderr
 * -> Will display all warning and notice to stdout
 *
 * @param int $errno Error number (e.g. E_ERROR, E_WARNING, ...)
 * @param string $errstr Error message
 */
function errorHandler($errno, $errstr, $errfile = null, $errline = 0)
{
    if (!$errfile) $errfile = __FILE__;
    
    // Is instruction causing the error preprended by @ symbol?
    if (error_reporting() == 0) return;
    
    // Format error message
    $message = "$errstr : $errfile";
    if ($errline) $message .= ", line #$errline";
    $message = '[' . date('Y-m-d H:i:s') . '] ' . str_replace("\n", " ", $message) . PHP_EOL;
    
    switch ($errno)
    {
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_CORE_WARNING:
        case E_COMPILE_ERROR:
        case E_COMPILE_WARNING:
        case E_USER_ERROR:
            // echo error to stdout, write error to stderr then die!
            echo 'ERR: ' . $message;
            echo 'ERR: [' . date('Y-m-d H:i:s') . '] ' . _TASK_HAS_ENDED_WITH_ERROR . PHP_EOL;
            file_put_contents('php://stderr', 'ERR: ' . $message);
            die();

        case E_WARNING:
        case E_USER_WARNING:
            // echo warning to stdout
            echo 'WRN: ' . $message;
            break;

        case E_NOTICE:
        case E_STRICT:
        case E_USER_NOTICE:
            // ignore notification and strict mode error if verbose is off
            $config = @wcmConfig::getInstance();
            if ($config && $config['wcm.logging.verbose'])
            {
                echo 'VRB: ' . $message;
            }
            break;

        default:
            // echo message to stdout
            echo 'MSG: ' . $message;
            break;
    }
}

// Catch all errors
set_error_handler('errorHandler');
        
// Retrieve parameters from CLI
$parameters = array();
foreach($argv as $argument)
{
    $parts = explode('=', $argument, 2);
    if (count($parts) == 2)
    {
        $arg = trim($parts[0], ' "');
        $val = trim($parts[1], ' "');
        $parameters[$arg] = $val;
    }
}

// Unserialize extra parameters
if (isset($parameters['_wcmParams']))
{
    $params = unserialize(base64_decode($parameters['_wcmParams']));
    $parameters = array_merge($parameters, $params);
}

// Init WCM api
require_once(dirname(__FILE__) . '/../../initApi.php');
$config  = wcmConfig::getInstance();
$project = wcmProject::getInstance();
$session = wcmSession::getInstance();

// Check parameters
if (!isset($parameters['_wcmLogPath']))
    errorHandler(E_ERROR, 'Missing _wcmLogPath parameter');

// write process id (PID)
$pidfile = $parameters['_wcmLogPath'] . 'pid.txt';
saveToFile($pidfile, getmypid());

// Check process file
if (!isset($parameters['_wcmProcess']))
    errorHandler(E_ERROR, 'Missing _wcmProcess parameter');

$process = $parameters['_wcmProcess'];
if (!file_exists($process))
    errorHandler(E_ERROR, 'Invalid process file: ' . $process);

// start a session with a valid user
$session->startSession(getArrayParameter($parameters, '_wcmUserId', wcmMembership::ROOT_USER_ID));

// Set session language
if (isset($parameters['_wcmLanguage']))
    $session->setLanguage($parameters['_wcmLanguage']);

// Set session working site
if (isset($parameters['_wcmSiteId']))
    $session->setSiteId($parameters['_wcmSiteId']);

// Prepare logger
$logger = new wcmLogger($config['wcm.logging.verbose'], $config['wcm.logging.debug'], STDOUT, false);

$logger->logMessage(_TASK_IS_STARTING);

try
{
    // Execute process file
    require($process);
}
catch(Exception $e)
{
    // Trigger error
    errorHandler(E_ERROR, 'Exception "' . $e->getMessage() . '"', $e->getFile(), $e->getLine());
}

// Remove pidfile when done
unlink($pidfile);

$logger->logMessage(_TASK_HAS_ENDED);
