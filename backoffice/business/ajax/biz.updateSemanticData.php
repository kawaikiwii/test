<?php

/**
 * Project:     WCM
 * File:        biz.updateSemanticData.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is meant to be invoked via
 * wcmBizAjaxController.call(). It updates the semantic data
 * associated with one or nore business object classes.
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve (some) parameters
$kindList    = getArrayParameter($_REQUEST, "kindList",    null);
$classList   = getArrayParameter($_REQUEST, "classList",   null);
$where       = getArrayParameter($_REQUEST, "where",       null);
$forceUpdate = getArrayParameter($_REQUEST, "forceUpdate", null);
$resultDivId = getArrayParameter($_REQUEST, "resultDivId", null);

// No browser cache
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s" )." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// XML output
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo "<response type='item' id='".$resultDivId."'>\n";

if (!$kindList)
{
    $kindList = array_keys(getSemanticDataKindList());
}
else
{
    $kindList = explode(',', $kindList);
}

if (!$classList)
{
    $classList = array_keys(getClassList());
}
else
{
    $classList = explode(',', $classList);
}

try
{
    updateSemanticData($project, $kindList, $classList, $where, $forceUpdate);
    echo _BIZ_UPDATE_SEMANTIC_DATA_SUCCEEDED;
}
catch (Exception $e)
{
    echo _BIZ_UPDATE_SEMANTIC_DATA_FAILED.': '.$e;
}

echo "</response>\n";
echo "</ajax-response>\n";

?>
