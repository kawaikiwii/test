<?php

/*
 * Project:     WCM
 * File:        job_import_afp.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
 * => This job has been designed to be invoked by "executeJob.php"
 *
 * This job is used to import a standard AFP web content
 * The AFP web content is supposed to be provided as a set of folders (one folder per channel)
 * Each folder containing an "index.xml" file pointing to a list of NEWSML files representing article and links to photos
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
    include("../api/biz.constants.php");
    require_once("biz.import.editorial.php");

    // Logger (verbose, no debug, no dump file, dont handle error)
    $logger = new wcmLogger(false, false, null, false);
    
    // Launch editorial import process
    $import = new importEditorial($session, "afp", $NEWSML_AFP, $PHOTOS_PATH, $logger, true);
    $import->process();

    // Display log in text mode (stdout)
    $logger->display(false);

    // Free allocated ressources 
    $logger->eraseLog();
    unset($import);
    unset($logger);
}
