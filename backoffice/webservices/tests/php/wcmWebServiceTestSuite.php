<?php

/**
 * Project:     WCM
 * File:        wcmWebServiceTestSuite.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

$thisDir = dirname(__FILE__);

require_once $thisDir.'/testSuite.php';

testSuite::run($thisDir.'/cases');

?>