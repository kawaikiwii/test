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
$className = getArrayParameter($_REQUEST, "className", '');

switch($className)
{
	case 'exportRule':
		$resultList = exportRule::getExportRules("title like '%".$prefix."%'");
		foreach ($resultList as $result)
		{
			$id = $result->id;
			$label = $result->title;
		}
		break;
	case 'account':
		$wcmUserId = wcmSession::getInstance()->userId;
		$resultList  = account::getAccounts($wcmUserId,"childs",$prefix);
		foreach ($resultList as $result)
		{
			$id = $result->id;
			$label = $result->wcmUser_name;
		}
		break;
}

if (count($resultList) < 1)
	echo 'invalid';
else
	echo $id.'#'.$label;
