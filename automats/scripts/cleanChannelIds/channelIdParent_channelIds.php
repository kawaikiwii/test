<?php 
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');
$link = mysql_connect("10.23.65.201","relaxweb","kzq!2007") or die("Connexion impossible");
$db_selected = mysql_select_db('RELAX_BIZ', $link);
$cpt = 0;
$query = "SELECT biz_news.id,biz_news.channelid,biz_channel.parentid FROM biz_news INNER JOIN biz_channel ON biz_news.channelid=biz_channel.id
		  AND biz_news.siteid=biz_channel.siteid WHERE biz_news.channelId IS NOT NULL AND biz_news.channelId<>0";
$result = mysql_query($query,$link);
while ($row = mysql_fetch_array($result)) {
	$id = $row["id"];
	$channelid = $row["channelid"];
	$parentid = $row["parentid"];
	if(!empty($channelid))
	{
		$query2 = "SELECT id,channelids,title,siteid FROM biz_news WHERE id=".$id." AND channelids<>'0' AND channelids IS NOT NULL
				   AND channelids NOT LIKE '%\"".$parentid."\"%' AND channelids NOT LIKE '%:".$parentid.";%'";
		$result2 = mysql_query($query2,$link) or die($query2);
		if ($row2 = mysql_fetch_array($result2)) {
			$cpt++;
	    	$channelids = $row2["channelids"];
	    	$title = $row2["title"];
	    	$siteid = $row2["siteid"];
	    	if(strpos($channelids,"s:") !== false)
	    	{
	    		$pos_indice_debut = strrpos($channelids,"i:")+2;
	    		$pos_indice_fin = strrpos($channelids,"s:")-2;
	    	}
	    	else
	    	{
	    		$pos_indice_debut = strrpos($channelids,"i:",(strrpos($channelids,"i:")-1)-strlen($channelids))+2;
	    		$pos_indice_fin = strrpos($channelids,";i")-1;
	    	}
	    	$new_indice = substr($channelids,$pos_indice_debut,$pos_indice_fin-$pos_indice_debut+1)+1;
	    	$new_indice_debut = $new_indice;
	    	$pos_channelids_fin = strrpos($channelids,";}");
	    	if(strpos($channelids,"s:") !== false)
	    		$insert_channelids = "i:".$new_indice.";s:".strlen($parentid).":\"".$parentid."\";";
	    	else
	    		$insert_channelids = "i:".$new_indice.":".$parentid.";";
	    	$obj = new news();
	    	$obj->refresh($id);
	    	$pilier = $obj->getPilier($channelid);
	    	if(!strstr($channelids,"\"".trim($pilier)."\"") && !strstr($channelids,":".trim($pilier).";")
	    		&& !strstr($insert_channelids,"\"".trim($pilier)."\"") && !strstr($insert_channelids,":".trim($pilier).";"))
	    	{
	    		$new_indice++;
	    		$insert_channelids .= "i:".$new_indice.";s:".strlen($pilier).":\"".$pilier."\";";
	    	}
	    	$new_indice++;
	    	$new_channelids = substr($channelids,0,$pos_channelids_fin+1).$insert_channelids."}";
	    	$new_channelids = substr($new_channelids,0,2).$new_indice.substr($new_channelids,strlen($new_indice)+2);
	    	$obj->channelIds = $new_channelids;
	    	$obj->save();
	    	echo $id." : ".$siteid." : ".$channelid." : ".$channelids." : ".$new_channelids." : ".$title."<br>";
	    	unset($obj);
	    	if($cpt >= 100)
	    		break;
	    }
	}
}
echo $cpt." news dont le channelId parent n'est pas dans les channelIds";
?>
