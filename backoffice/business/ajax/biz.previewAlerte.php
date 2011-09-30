<?php

/**
 * Project:     WCM
 * File:        biz.alerte.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

date_default_timezone_set('Europe/Paris');

// Get current project
$project = wcmProject::getInstance();

$alerteId = getArrayParameter($_REQUEST, "alerteId", null);
$loginAs  = getArrayParameter($_REQUEST, "loginAs", null);

$relaxTask = new relaxTask();
if ($alerteId)
{
	$relaxTask->refresh($alerteId);
	$relaxTask->serializedForm = unserialize($relaxTask->serializedForm);
}

$date = date('Y-m-d H:i:s');
$LAST_7_DAYS = strtotime($date.' - 7 days');
$search = array('__LAST_PUSH__', '__NEXT__');
$replace = array(date('Y-m-d\TH:i:s', $LAST_7_DAYS), str_replace(' ', 'T', $date));
$query = str_replace($search, $replace, $relaxTask->query);
$config = wcmConfig::getInstance();
$search = wcmBizsearch::getInstance($config['wcm.search.engine']);
$uid = 'relaxTask_'.$relaxTask->processId.'_'.uniqid();
$total = $search->initSearch($uid, $query, "publicationdate desc, publicationtime desc", "FO");
//echo $query;
// Response
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<ajax-response>\n";
echo "<response type=\"item\" id=\"alerte\"><![CDATA[";
	echo "<div style='overflow:auto;max-height:500px;'>";
	echo "<div style='width: 80%; float: left;'>Test pour la tâche ".$relaxTask->name." du ".date('d/m/Y H:i:s', $LAST_7_DAYS)." au ".date('d/m/Y H:i:s')."</div>";
	echo "<ul class='toolbar newtask'>";
	echo '<li><a href="#" onclick="manageAlerte(\'refresh\','.$loginAs.','.$alerteId.',\'\'); return false;" class="cancel">'._PM_RETURN.'</a></li>';
	echo "</ul>";
	if ($total > 0) {
		foreach($search->getDocumentRange(0, $total, $uid, false) as $item)
		{
			if (trim($item->title) != "") {
				echo "<div>";
				echo "<b>".$item->title."</b> (".$item->publicationDate.")<br />";
				echo $item->getDescription();
				echo "</div>";
			}
		}
	}
	echo "</div>";
	
echo "]]></response>";
echo "</ajax-response>";
