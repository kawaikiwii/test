<?php

/**
 * Project:     WCM
 * File:        biz.contributionviews.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call, It returns a list of contributions based on the state and the context
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

header('Content-Type: text/xml;charset=UTF-8');

$state          = getArrayParameter($_REQUEST, "state", null);
$className      = getArrayParameter($_REQUEST, "className", null);
$objectId       = getArrayParameter($_REQUEST, "objectId", 0);
$from           = getArrayParameter($_REQUEST, "from", 0);
$limit          = getArrayParameter($_REQUEST, "limit", 25);
$showmore        = getArrayParameter($_REQUEST, "showmore", 0);

if ($objectId)
{
    $bizobject = new $className(null, $objectId);
}

$params = array();
$params["where"] = "referentClass='".get_class($bizobject)."' AND referentId='".$bizobject->id."'";
$params["from"] = $from;
$params["limit"] = $limit;
$params["state"] = $state;
$params["showmore"] = $showmore;

$params["fields"] = array("title", "nickname", "createdAt", "workflowState");
$params["fieldtitles"] = array(_DASHBOARD_MODULE_HEADER_TITLE, _DASHBOARD_MODULE_HEADER_BYLINE, _DASHBOARD_MODULE_HEADER_MODIFICATION_DATE, _WORKFLOW_STATE);

$params["source"] = "contribution";
$params["orderby"] = "createdAt ASC";
	 	

switch($state)
{
    case "awaiting":
    	$params["where"] .= " AND workflowState!='approved' AND workflowState!='rejected'";
    break;
    case "all":
    	$params["where"] .= "";
    break;
    case "approved":
    	$params["where"] .= " AND workflowState = 'approved'";
    break;
    case "rejected":
    	$params["where"] .= " AND workflowState = 'rejected'";
    break;
    
}
	
	$parameters['params'] = $params;
	$generator = new wcmTemplateGenerator();
    echo $generator->executeTemplate('dashboard/db.contributiontab.tpl', $parameters);
	
	
 ?>
