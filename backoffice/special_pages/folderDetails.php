<?php
/**
 * Project:     Search channelid and channelids used in enabled tasks 
 * File:        tasks.php
 *
 * @copyright   LSJ
 *
 */
// Initialize wcm
require_once(dirname( __FILE__ ).'/../initWebApp.php');

$config = wcmConfig::getInstance();

$db = new wcmDatabase(str_replace(":3306","",$config['wcm.businessDB.connectionString']));
$query = "SELECT * FROM `biz_folder` WHERE createdAt >= '2010-01-01 00:00:00' ORDER BY biz_folder.siteid DESC";
$result = mysql_query($query) or die(mysql_error());
echo "<html><body>";

while($row = mysql_fetch_assoc($result)) {
	switch($row["siteId"]){
		case 4:
			$site_name = "AFPRelax En";
		break;
		case 5:
			$site_name = "AFPRelax Fr";
		break;
		case 6:
			$site_name = "Relaxfil";
		break;
		default:
			$site_name = "Univers partenaires";	
		break;
	}
	echo "<br /><b>".$site_name."<br />Dossier : ".$row["title"]."<br />Date de cr&eacute;ation : ".$row["createdAt"]."</b><br />";
	echo "<b>siteId:".$row["siteId"]." AND folderId:".$row["id"]." AND createdAt:[2010-01-01T00:00:00 TO *]</b><br /><br />";
	
	/*$news_query = "SELECT count(id) AS count_news FROM biz_news WHERE folderIds LIKE '%".$row["id"]."%'";
	$news_result = mysql_query($news_query) or die(mysql_error());
	while($news_row = mysql_fetch_assoc($news_result)) {
		//echo "Nb News : ".$news_row["count_news"]."<br />";
	}
	$slideshow_query = "SELECT count(id) AS count_slideshow FROM biz_slideshow WHERE folderIds LIKE '%".$row["id"]."%'";
        $slideshow_result = mysql_query($slideshow_query) or die(mysql_error());
        while($slideshow_row = mysql_fetch_assoc($slideshow_result)) {
                //echo "Nb Slideshow : ".$slideshow_row["count_slideshow"]."<br />";
        }
	$video_query = "SELECT count(id) AS count_video FROM biz_slideshow WHERE folderIds LIKE '%".$row["id"]."%'";
        $video_result = mysql_query($video_query) or die(mysql_error());
        while($video_row = mysql_fetch_assoc($video_result)) {
                //echo "Nb Video : ".$video_row["count_video"]."<br />";
        }*/				
}

/*function traite_chaine($text) {
	global $channels;
	global $task_channels;
	global $task_channel_names;
	if(substr($text,0,1)=="(") {
		$pos = strpos($text,")");
		$channel = substr($text,1,$pos -1);
		if(substr_count($channel,",")>0) {
			$arr_mult_channels = explode(",",$channel);
			if($task_channels != "") $task_channels .= ",";
			if($task_channel_names != "") $task_channel_names .= ",";
			$nb=0;
			foreach($arr_mult_channels as $value) {
				$nb++;
				$sep = ($nb < count($arr_mult_channels)) ? "," : "";
				$channels[] = $value;
				$task_channels .= $value.$sep;
				$objChannel = new channel();
				$objChannel->refresh($value);
				$task_channel_names .= $objChannel->title.$sep;
			}
		} else if(substr_count($channel," or ")>0) {
			$arr_mult_channels = explode(" or ",$channel);
			if($task_channels != "") $task_channels .= ",";
			if($task_channel_names != "") $task_channel_names .= ",";
			$nb=0;
			foreach($arr_mult_channels as $value) {
				$nb++;
				$sep = ($nb < count($arr_mult_channels)) ? "," : "";
				$channels[] = $value;
				$task_channels .= $value.$sep;
				$objChannel = new channel();
				$objChannel->refresh($value);
				$task_channel_names .= $objChannel->title.$sep;
			}
		} else {
			$channels[] = $channel;
			if($task_channels != "") $task_channels .= ",";
			if($task_channel_names != "") $task_channel_names .= ",";
			$task_channels .= $channel;
			$objChannel = new channel();
			$objChannel->refresh($channel);
			$task_channel_names .= $objChannel->title;
		}
	} else if(substr_count($text," ")>0) {
		if($task_channels != "") $task_channels .= ",";
		if($task_channel_names != "") $task_channel_names .= ",";
		$pos = strpos($text," ");
		$channel = str_replace(")","",substr($text,0,$pos));
		$channels[] = $channel;
		$task_channels .= $channel;
		$objChannel = new channel();
		$objChannel->refresh($channel);
		$task_channel_names .= $objChannel->title;
	} else if(substr_count($text,")")>0) {
		if($task_channels != "") $task_channels .= ",";
		if($task_channel_names != "") $task_channel_names .= ",";
		$pos = strpos($text,")");
		$channel = substr($text,0,$pos);
		$channels[] = $channel;
		$task_channels .= $channel;
		$objChannel = new channel();
		$objChannel->refresh($channel);
		$task_channel_names .= $objChannel->title;
	} else if(substr($text,0,3)!="not") {
		if($task_channels != "") $task_channels .= ",";
		if($task_channel_names != "") $task_channel_names .= ",";
		$channel = $text;
		$channels[] = $channel;
		$task_channels .= $channel;
		$objChannel = new channel();
		$objChannel->refresh($channel);
		$task_channel_names .= $objChannel->title;
	}
}

echo "<table border=\"1\">";
while($row = mysql_fetch_assoc($result)) {
	$task_channels = "";
	$task_channel_names = "";
	$query = strtolower($row['query']);
	if(substr_count($query,"channelid:")>0 || substr_count($query,"channelids:")>0) {
		echo "<tr>";
		echo "<td>&nbsp;".$row['id']."</td>";
		echo "<td>&nbsp;".$row['name']."</td>";
		//***** channelid *****
		echo "<td>&nbsp;";
		if(substr_count($query,"channelid:")>0) {
			$tabChannel = explode("channelid:",$query);
			for($n=1; $n<count($tabChannel) ;$n++) {
				traite_chaine($tabChannel[$n]);
			}
		}
		//***** channelids *****
		if(substr_count($query,"channelids:")>0) {
			$tabChannel = explode("channelids:",$query);
			for($n=1; $n<count($tabChannel) ;$n++) {
				traite_chaine($tabChannel[$n]);
			}
		}
		echo $task_channels;
		echo "<br/>".$task_channel_names;
		echo "</td>";
		echo "<td>&nbsp;".$query."</td>";
		echo "</tr>";
	}
}
echo "</table>";

natsort($channels);
$oldvalue = "";
foreach($channels as $value) {
	if($value != $oldvalue) {
		$lstChannels = $lstChannels.$value.",";
		$oldvalue = $value;
		$nbChannels++;
	}
}
echo "<br/><br/>ChannelId (ou ChannelIds) = ".substr($lstChannels,0,strlen($lstChannels)-1);
echo "<br/><br/>Nbre ChannelId (ou ChannelIds) utilis&#233;s = <b>".$nbChannels."</b>";*/
echo "<br/><br/></body></html>";
?>
