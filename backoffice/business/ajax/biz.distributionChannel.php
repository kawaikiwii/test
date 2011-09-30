<?php

/**
 * Project:     WCM
 * File:        biz.distributionChannel.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve parameters
$command      = getArrayParameter($_REQUEST, "command", null);
$exportRuleId = getArrayParameter($_REQUEST, "exportRuleId", 0);
$itemId       = getArrayParameter($_REQUEST, "itemId", 0);
$divId        = getArrayParameter($_REQUEST, "divId", 0);
$formDatas    = getArrayParameter($_REQUEST, "formDatas", null);

$msg = '';

$exportRule = new exportRule();
$exportRule->refresh($exportRuleId);

$formsDatasArray = array();
if ($formDatas)
{
	$groups = array();
	$temp = explode('&',$formDatas);
	foreach ($temp as $item)
	{
		$temp2 = explode('=',$item);
		$formsDatasArray[urldecode($temp2[0])] = urldecode($temp2[1]);
	}
	if ($formsDatasArray['type'])
	{
		switch ($formsDatasArray['type'])
		{
			case 'ftp':
				$arrayParameters = array(
					"host"           => $formsDatasArray['host'],
					"user"           => $formsDatasArray['user'],
					"pass"           => $formsDatasArray['pass'],
					"remotePath_ftp" => $formsDatasArray['remotePath_ftp']
					);
				$formsDatasArray['connexionString'] = serialize($arrayParameters);
				break;
			case 'fs':
				$arrayParameters = array(
					"remotePath_fs" => $formsDatasArray['remotePath_fs']
					);
				$formsDatasArray['connexionString'] = serialize($arrayParameters);
				break;
			case 'email':
				$arrayParameters = array(
					"fromName" => $formsDatasArray['fromName'],
					"fromMail" => $formsDatasArray['fromMail'],
					"to"       => $formsDatasArray['to'],
					"title"    => $formsDatasArray['title']
					);
				$formsDatasArray['connexionString'] = serialize($arrayParameters);
				break;
		}
	}
}

if ($command == 'insert' || $command =='update')
{
	if ($formsDatasArray['code'] == '')
	{
		$divId = "errorMsg";
		$msg = _CODE_MANDATORY;
	}
}

if ($msg == '')
{
	switch($command)
	{
		case "insert":
 		        $distributionChannel = new distributionChannel();
			$distributionChannel->bind($formsDatasArray);
			if(!$distributionChannel->save())
			{
				$divId = "errorMsg";
				$msg = $distributionChannel->getErrorMsg();
			}
			break;

		case "update":
 		        $distributionChannel = new distributionChannel();
 		        $distributionChannel->refresh($itemId);
			$distributionChannel->bind($formsDatasArray);
			if(!$distributionChannel->save())
			{
				$divId = "errorMsg";
				$msg = $distributionChannel->getErrorMsg();
			}
			break;

		case "delete":
			$distributionChannel = new distributionChannel();
			$distributionChannel->refresh($itemId);
			$distributionChannel->delete();
			break;
	}
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo "<response type='item' id='".$divId."'><![CDATA[";
if ($msg != '')
	echo "<div style=\"background-color:red; text-align: center; font-weight:bolder;\">".$msg."</div>";
else
{
	echo "<table id='distributionChannels'>";
	echo "<tr>";
	echo "<th width='30'>&nbsp;</th>";
	echo "<th>"._DASHBOARD_MODULE_DISTRIBUTIONCHANNEL_CODE."</th>";
	echo "<th>"._DASHBOARD_MODULE_DISTRIBUTIONCHANNEL_TYPE."</th>";
	echo "<th>"._DASHBOARD_MODULE_DISTRIBUTIONCHANNEL_ACTIVE."</th>";
	echo "<th>&nbsp;</th>";
	echo "</tr>";

        $typeList = distributionChannel::getTypeList();
        
        $distributionChannelsArray = $exportRule->getDistributionChannels(true);
        if (count($distributionChannelsArray)>0)
        {
 		$i=0;
       		foreach ($distributionChannelsArray as $currentDistributionChannel)
       		{
			if ($i%2==0)
				echo "<tr id='account_".$currentDistributionChannel->id."'>";
			else
				echo "<tr id='account_".$currentDistributionChannel->id."' class='alternate'>";
			echo "<td class='actions'>";
			echo "<ul class='two-buttons'>";
				echo "<li><a class='edit' title='"._EDIT."' href=\"javascript:openmodal('"._UPDATE_DISTRIBUTIONCHANNEL."','500'); modalPopup('distributionChannel','update', '".$currentDistributionChannel->id."', '".$exportRuleId."', '');\"><span>"._EDIT."</span></a></li>";
				echo "<li><a class='delete' title='"._DELETE."' href=\"javascript: if (confirm('"._DISTRIBUTIONCHANNEL_DELETE_CONFIRM."')) (ajaxDistributionChannel('delete', '".$exportRuleId."','".$currentDistributionChannel->id."', '".$divId."',''));\" id=''><span>"._DELETE."</span></a></li>";
			echo "</ul>";
			echo "</td>";
			echo "<td align='center'>";
				echo $currentDistributionChannel->code;
			echo "</td>";
			echo "<td align='center'>";
				echo $typeList[$currentDistributionChannel->type];
			echo "</td>";
			echo "<td align='center'>";
				if ($currentDistributionChannel->active)
					echo "<img src=img/grant.gif>";
				else
					echo "<img src=img/deny.gif>";
			echo "</td>";
			echo "<td align='center'>&nbsp;</td>";
			echo "</tr>";
			$i++;
		}
	}
	else
	{
		echo "<tr><td colspan='6'> - ("._EMPTY.") - </td></tr>";
	}
	echo "</table>";
}
echo "]]></response>\n";
echo "</ajax-response>";

?>
