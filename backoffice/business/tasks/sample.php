<?php
/**
 * A basic test for the task manager
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
 * should be raised using Exception
 */

$i = 0;
while ($i < 10)
{
    $i++;
    sleep(2);
    $logger->logMessage('Test - Iteration #' . $i);
    if (isset($parameters['stopAt']) && $is == intval($parameters['stopAt']))
    {
        throw new Exception('Stop loop by throwing exception!');
    }
}