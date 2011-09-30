<?php

/*
 * Project:     WCM
 * File:        job_purge.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * => This job has been designed to be invoked by "executeJob.php"
 * 
 * This job purge all expired articles
 *
 */

/*
 * A "job" must declare a public "doTheJob" function to be launch
 *
 * @param wcmSession $session A valid session to WCM project
 *
*/
function doTheJob($session)
{
    // Logger (verbose, no debug, no dump file, dont handle error)
    $logger = new wcmLogger(false, false, null, false);
    
    // 7 days ago
    $j7 = date("Y-m-d H:i:s",mktime(0,0,0, date("m"), date("d")-7, date("Y")));

    // Purge...
    // TODO
    
    // Clean resources    
    $logger->eraseLog();
    unset($logger);
}
