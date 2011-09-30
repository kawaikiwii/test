<?php

/**
 * Project:     WCM
 * File:        ajax/wcm.workflow.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * Ajax to execute a transition
 * Needs to set the context
 */

// Initialize the system
require_once dirname(__FILE__) . '/../initWebApp.php';

$className    = getArrayParameter($_REQUEST, 'className', null);
$id           = getArrayParameter($_REQUEST, 'id', null);
$transitionId = getArrayParameter($_REQUEST, 'transitionId', null);

$object = new $className();
$object->refresh($id);

$transition = new wcmWorkflowTransition();
$transition->refresh($transitionId);
if ($object->executeTransition($transition))
{
	return true;
}