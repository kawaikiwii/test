<?php

/*
 * Project:     WCM
 * File:        executeJob.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    // User and password
    $USERNAME = 'robot';
    $PASSWORD = 'r0b0t!';

    // Check call
    if (!isset($argc))
    {
        die("Job can only be invoked in command line mode");
    }

    // Check parameters
    if ($argc < 2)
    {
        die('Usage: executeJob.php <jobname> [extra parameters]');
    }

    $jobname = 'job_' . $_SERVER["argv"][1] . '.php';
   
    // Check job existence
    if (!file_exists($jobname))
    {
        die('Job '.$jobname.' not found');
    }

    // Load and instanciate API
    $app_dir = dirname(dirname(__FILE__));
    
    require_once $app_dir . '/initApi.php';

    // Open new session
    $session = wcmSession::getInstance();
    $project =& wcmProject::getInstance();
    if (!$session->login($USERNAME, $PASSWORD))
       die('Connection failed; invalid user or password');
    
    // Launch job...
    require($jobname);
    doTheJob($session);
?>
