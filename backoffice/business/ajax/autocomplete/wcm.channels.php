<?php
/**
 * Project:     WCM
 * File:        ajax/autocomplete/wcm.exportRules.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize the system
require_once dirname(__FILE__).'/../../../initWebApp.php';
$config    = wcmConfig::getInstance();
$prefix    = getArrayParameter($_REQUEST, "prefix", '');

$channels = explode('/', $prefix);
$parentId = '';
$retId = '';
$retLabel = '';
$where ='';
foreach ($channels as $channel)
{
	if ($parentId != '')
		$where = ' AND parentId = '.$parentId;
	$resultList = bizobject::getBizobjects('channel', "title = '".$channel."'".$where);
	foreach ($resultList as $result)
	{
		$parentId = $result->parentId;
		$id = $result->id;
		$label = $result->title;
		$retId .= $id.'/';
		$retLabel .= $label.'/';
	}
}


if ($retId == '')
	echo 'invalid';
else
	echo substr($retId,0,-1).'#'.substr($retLabel,0,-1);

