<?php
/**
 * Project:     WCM
 * File:        wcm.ajaxManagePermissions.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

$html = _UPDATE_SUCCESSFUL;
$permissions = array();
foreach ($_REQUEST as $key => $value)
{
    // assuming we receive "P_{permissionValue}_{groupId}_{permissionTarget}"
    @list($prefix, $permissionValue, $groupId, $permissionTarget) = explode('_', $key, 4);
    if ($prefix == 'P')
    {
        // need to sum permissions for each instance of permissionTarget for a specific group
        $p  = (isset($permissions[$permissionTarget])) ? getArrayParameter($permissions[$permissionTarget], $groupId, 0) : 0;
        if ($value) $p |= intval($permissionValue);
        $permissions[$permissionTarget][$groupId] = $p;
    }
}

foreach ($permissions as $permissionTarget => $groupPermissions)
{
    foreach ($groupPermissions as $groupId => $p)
    {
        $parameters = array($groupId, $permissionTarget, $p);
        $sql = 'delete from #__permission where groupId=? and target=?';
        $project->database->executeStatement($sql, $parameters);
        $sql = 'insert into #__permission(groupId, target, permissions) values(?, ?, ?)';
        $project->database->executeStatement($sql, $parameters);
    }
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";

// Write ajax response
echo '<ajax-response>' . "\n";
echo '<response type="item" id="' . $_REQUEST['divWait'] . '">' . $html . '</response>'. "\n";
echo '</ajax-response>'. "\n";

function removeBox($perm)
{   
    return (!(strpos($perm, 'box_') === 0));
}
?>